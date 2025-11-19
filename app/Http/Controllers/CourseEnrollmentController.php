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

        // Get all companies for the filter dropdown
        $companies = \App\Models\Company::orderBy('name')->get();

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

        // Get all users (both enrolled and not enrolled) for display
        $query = User::with('companies')
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'admin');
            });

        // Filter by company if selected
        if ($request->has('company_id') && $request->company_id) {
            $query->whereHas('companies', function($q) use ($request) {
                $q->where('companies.id', $request->company_id);
            });
        }

        $allUsers = $query->orderBy('name')->get();

        return view('courses.enrollments.create', compact('course', 'allUsers', 'companies', 'enrolledUserIds', 'enrolledCompanyIds'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('manageStudents', $course);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:enrolled,in_progress',
            'notes' => 'nullable|string',
        ]);

        $enrolled = 0;
        foreach ($request->user_ids as $userId) {
            // Check if already enrolled
            $exists = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $userId)
                ->exists();

            if (!$exists) {
                $user = User::find($userId);
                $enrollment = CourseEnrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $userId,
                    'company_id' => $user->primary_company?->id,
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
                $user->notify(new \App\Notifications\CourseEnrollmentNotification($course, $enrollment));

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

        return redirect()->route('courses.enrollments.index', $course)
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

        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'company_id' => $user->primary_company?->id,
            'status' => 'enrolled',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        logActivity('course_self_enrolled', 'CourseEnrollment', $course->id, [
            'course_title' => $course->title,
            'user_name' => $user->name,
        ]);

        // Send enrollment notification email
        $user->notify(new \App\Notifications\CourseEnrollmentNotification($course, $enrollment));

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
