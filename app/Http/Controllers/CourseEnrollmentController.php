<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseEnrollmentController extends Controller
{
    public function index(Course $course)
    {
        $this->authorize('manageStudents', $course);

        $enrollments = $course->enrollments()
            ->with('user', 'company')
            ->orderBy('enrolled_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $course->enrollments()->count(),
            'enrolled' => $course->enrollments()->where('status', 'enrolled')->count(),
            'in_progress' => $course->enrollments()->where('status', 'in_progress')->count(),
            'completed' => $course->enrollments()->where('status', 'completed')->count(),
            'average_progress' => $course->enrollments()->avg('progress_percentage') ?? 0,
        ];

        return view('courses.enrollments.index', compact('course', 'enrollments', 'stats'));
    }

    public function create(Request $request, Course $course)
    {
        $this->authorize('manageStudents', $course);

        // Get companies based on user role
        if (auth()->user()->hasRole('company_manager')) {
            // Company managers only see their own companies
            $companies = auth()->user()->companies()->orderBy('name')->get();
        } else {
            // STA managers and teachers see all companies
            $companies = \App\Models\Company::orderBy('name')->get();
        }

        // Get currently enrolled users with their company info
        $enrolledUsers = $course->enrollments()
            ->with('user.companies')
            ->get()
            ->pluck('user');
        $enrolledUserIds = $enrolledUsers->pluck('id')->toArray();

        // Get companies that have enrolled users in this course
        $enrolledCompanyIds = $enrolledUsers->flatMap(function($user) {
            return $user->companies->pluck('id');
        })->unique()->toArray();

        // Get the course's assigned company (first one if multiple)
        $defaultCompanyId = $course->assignedCompanies->first()?->id;

        // Get all users (both enrolled and not enrolled) for display
        $query = User::with('companies')
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'admin');
            });

        // If company manager, filter to only show users from their companies
        if (auth()->user()->hasRole('company_manager')) {
            $userCompanyIds = auth()->user()->companies->pluck('id')->toArray();
            $query->whereHas('companies', function($q) use ($userCompanyIds) {
                $q->whereIn('companies.id', $userCompanyIds);
            });
        }

        // Filter by company if selected
        if ($request->has('company_id') && $request->company_id) {
            $query->whereHas('companies', function($q) use ($request) {
                $q->where('companies.id', $request->company_id);
            });
        }

        $allUsers = $query->orderBy('name')->get();

        return view('courses.enrollments.create', compact('course', 'allUsers', 'companies', 'enrolledUserIds', 'enrolledCompanyIds', 'defaultCompanyId'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('manageStudents', $course);

        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:enrolled,in_progress',
            'notes' => 'nullable|string',
            'selected_company_id' => 'nullable|exists:companies,id',
        ]);

        // Log the received data for debugging
        \Log::info('Enrollment Request Data', [
            'selected_company_id' => $request->selected_company_id,
            'user_ids' => $request->user_ids,
            'all_request' => $request->all()
        ]);

        // Update course's assigned company if a company was selected
        // Only ONE company is allowed, so replace any existing assignments
        if ($request->selected_company_id) {
            // Check if this company is already the assigned company
            $currentAssignedCompany = $course->assignedCompanies->first();

            if (!$currentAssignedCompany || $currentAssignedCompany->id != $request->selected_company_id) {
                // Delete all existing company assignments (only one company allowed)
                $course->companyAssignments()->delete();

                // Create new company assignment
                $course->companyAssignments()->create([
                    'company_id' => $request->selected_company_id,
                    'assigned_by' => auth()->id(),
                    'assigned_date' => now(),
                    'is_mandatory' => false,
                ]);

                \Log::info('Replaced course assigned company', [
                    'course_id' => $course->id,
                    'old_company_id' => $currentAssignedCompany?->id,
                    'new_company_id' => $request->selected_company_id,
                ]);
            }
        }

        // If no users selected, redirect back with info message
        if (empty($request->user_ids)) {
            return redirect()->to(courseManagementRoute('show', $course))
                ->with('info', 'No users were enrolled as none were selected.');
        }

        $enrolled = 0;
        foreach ($request->user_ids as $userId) {
            // Check if already enrolled
            $exists = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $userId)
                ->exists();

            if (!$exists) {
                $user = User::find($userId);

                // Determine company_id: MUST use the selected company or course's assigned company
                // Priority: selected_company_id > course's first assigned company
                // DO NOT fall back to user's primary company to ensure consistency
                $companyId = $request->selected_company_id
                    ?: $course->assignedCompanies->first()?->id;

                \Log::info('Creating enrollment', [
                    'user_id' => $userId,
                    'selected_company_id' => $request->selected_company_id,
                    'course_assigned_company_id' => $course->assignedCompanies->first()?->id,
                    'final_company_id' => $companyId
                ]);

                $enrollment = CourseEnrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $userId,
                    'company_id' => $companyId,
                    'status' => $request->status,
                    'enrolled_at' => now(),
                    'progress_percentage' => 0,
                    'notes' => $request->notes,
                ]);

                logActivity('course_enrollment_created', 'CourseEnrollment', $course->id, [
                    'course_title' => $course->title,
                    'user_name' => $user->name,
                    'user_id' => $userId,
                ]);

                // Send enrollment notification email
                try {
                    $user->notify(new \App\Notifications\CourseEnrollmentNotification($course, $enrollment));
                } catch (\Exception $e) {
                    // Log the error but don't fail the enrollment
                    \Log::warning('Failed to send enrollment notification email', [
                        'user_id' => $userId,
                        'course_id' => $course->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Add course calendar event for this user if course has schedule
                if ($course->start_date && $course->end_date) {
                    \App\Models\CourseEvent::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'user_id' => $userId,
                        ],
                        [
                            'title' => $course->title,
                            'description' => $course->description ?? 'Course: ' . $course->title,
                            'start_date' => $course->start_date,
                            'start_time' => $course->start_time,
                            'end_date' => $course->end_date,
                            'end_time' => $course->end_time,
                            'status' => 'scheduled',
                        ]
                    );
                }

                $enrolled++;
            }
        }

        return redirect()->to(courseManagementRoute('show', $course))
            ->with('success', "{$enrolled} user(s) enrolled successfully.");
    }

    public function edit(CourseEnrollment $enrollment)
    {
        $this->authorize('manageStudents', $enrollment->course);

        $course = $enrollment->course;
        return view('courses.enrollments.edit', compact('enrollment', 'course'));
    }

    public function update(Request $request, CourseEnrollment $enrollment)
    {
        $this->authorize('manageStudents', $enrollment->course);

        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|in:enrolled,in_progress,completed,dropped,failed',
            'final_score' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $enrollment->update([
            'progress_percentage' => $request->progress_percentage,
            'status' => $request->status,
            'final_score' => $request->final_score,
            'grade' => $request->grade,
            'notes' => $request->notes,
        ]);

        // Mark as completed if status is completed
        if ($request->status === 'completed' && !$enrollment->completed_at) {
            $enrollment->update(['completed_at' => now()]);
        }

        logActivity('course_enrollment_updated', 'CourseEnrollment', $enrollment->id, [
            'course_title' => $enrollment->course->title,
            'user_name' => $enrollment->user->name,
            'progress' => $request->progress_percentage,
            'status' => $request->status,
        ]);

        return redirect()->route('courses.enrollments.index', $enrollment->course)
            ->with('success', 'Enrollment updated successfully.');
    }

    public function updateProgress(Request $request, CourseEnrollment $enrollment)
    {
        $this->authorize('manageStudents', $enrollment->course);

        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|in:enrolled,in_progress,completed,dropped,failed',
            'final_score' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $enrollment->update([
            'progress_percentage' => $request->progress_percentage,
            'status' => $request->status,
            'final_score' => $request->final_score,
            'grade' => $request->grade,
            'notes' => $request->notes,
        ]);

        // Mark as completed if status is completed
        if ($request->status === 'completed' && !$enrollment->completed_at) {
            $enrollment->update(['completed_at' => now()]);
        }

        logActivity('course_enrollment_updated', 'CourseEnrollment', $enrollment->id, [
            'course_title' => $enrollment->course->title,
            'user_name' => $enrollment->user->name,
            'progress' => $request->progress_percentage,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Progress updated successfully.');
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $this->authorize('manageStudents', $enrollment->course);

        logActivity('course_enrollment_deleted', 'CourseEnrollment', $enrollment->id, [
            'course_title' => $enrollment->course->title,
            'user_name' => $enrollment->user->name,
        ]);

        $enrollment->delete();

        return redirect()->back()->with('success', 'Enrollment deleted successfully.');
    }

    // Student-facing methods
    public function myCourses()
    {
        $user = Auth::user();

        $enrollments = CourseEnrollment::with('course.teacher')
            ->where('user_id', $user->id)
            ->orderBy('enrolled_at', 'desc')
            ->paginate(12);

        $stats = [
            'total' => $enrollments->total(),
            'in_progress' => CourseEnrollment::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'completed' => CourseEnrollment::where('user_id', $user->id)->where('status', 'completed')->count(),
            'average_progress' => CourseEnrollment::where('user_id', $user->id)->avg('progress_percentage') ?? 0,
        ];

        return view('courses.my-courses', compact('enrollments', 'stats'));
    }

    public function catalog()
    {
        $user = Auth::user();

        // Get enrolled course IDs
        $enrolledCourseIds = CourseEnrollment::where('user_id', $user->id)->pluck('course_id')->toArray();

        // Get available courses (not enrolled, active)
        $courses = Course::active()
            ->whereNotIn('id', $enrolledCourseIds)
            ->with('teacher')
            ->orderBy('title')
            ->paginate(12);

        return view('courses.catalog', compact('courses'));
    }

    public function enroll(Course $course)
    {
        $user = Auth::user();

        // Check if already enrolled
        $exists = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        // Check max participants
        if ($course->max_participants) {
            $currentEnrollments = $course->enrollments()->whereIn('status', ['enrolled', 'in_progress'])->count();
            if ($currentEnrollments >= $course->max_participants) {
                return redirect()->back()->with('error', 'This course is full.');
            }
        }

        // Use course's assigned company to ensure consistency
        // Priority: course's first assigned company > user's primary company
        $companyId = $course->assignedCompanies->first()?->id
            ?: $user->primary_company?->id;

        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'company_id' => $companyId,
            'status' => 'enrolled',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        logActivity('course_self_enrolled', 'CourseEnrollment', $course->id, [
            'course_title' => $course->title,
            'user_name' => $user->name,
        ]);

        // Send enrollment notification email
        try {
            $user->notify(new \App\Notifications\CourseEnrollmentNotification($course, $enrollment));
        } catch (\Exception $e) {
            // Log the error but don't fail the enrollment
            \Log::warning('Failed to send self-enrollment notification email', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
        }

        // Add course calendar event for this user if course has schedule
        if ($course->start_date && $course->end_date) {
            \App\Models\CourseEvent::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ],
                [
                    'title' => $course->title,
                    'description' => $course->description ?? 'Course: ' . $course->title,
                    'start_date' => $course->start_date,
                    'start_time' => $course->start_time,
                    'end_date' => $course->end_date,
                    'end_time' => $course->end_time,
                    'status' => 'scheduled',
                ]
            );
        }

        return redirect()->route('my-courses')->with('success', 'Successfully enrolled in the course!');
    }
}
