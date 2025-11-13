<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\AuditLogService;

class CoursesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::query();

        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        if ($request->has('level') && $request->level) {
            $query->byLevel($request->level);
        }

        if ($request->has('delivery_method') && $request->delivery_method) {
            $query->where('delivery_method', $request->delivery_method);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('instructor', 'like', "%{$search}%");
            });
        }

        if (!$request->has('show_inactive')) {
            $query->active();
        }

        $courses = $query->with('teacher')->orderBy('title')->paginate(12);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();

        return view('courses.index', compact('courses', 'categories', 'levels', 'deliveryMethods'));
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();
        $statuses = Course::getStatuses();

        // Get users with teacher role
        $teachers = User::role('teacher')->orderBy('name')->get();

        return view('courses.create', compact('categories', 'levels', 'deliveryMethods', 'statuses', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'category' => 'required|string',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'credits' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'instructor' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'prerequisites' => 'nullable|string',
            'delivery_method' => 'required|string|in:online,offline,hybrid',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_mandatory' => 'boolean',
            'status' => 'required|string|in:active,inactive,ongoing,done',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable',
        ]);

        $course = Course::create($validated);

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Course created successfully.');
    }

    public function show(Course $course): View
    {
        $course->load(['materials.uploader', 'teacher']);
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();
        $statuses = Course::getStatuses();

        // Get users with teacher role
        $teachers = User::role('teacher')->orderBy('name')->get();

        return view('courses.edit', compact('course', 'categories', 'levels', 'deliveryMethods', 'statuses', 'teachers'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $course->id,
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'category' => 'required|string',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'credits' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'instructor' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'prerequisites' => 'nullable|string',
            'delivery_method' => 'required|string|in:online,offline,hybrid',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_mandatory' => 'boolean',
            'status' => 'required|string|in:active,inactive,ongoing,done',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable',
        ]);

        // Log course status change if applicable
        if (isset($validated['is_active']) && $course->is_active !== $validated['is_active']) {
            $statusText = $validated['is_active'] ? 'activated' : 'deactivated';
            AuditLogService::logCustom(
                'course_status_changed',
                "Course '{$course->title}' was {$statusText}",
                'courses',
                'info',
                [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'old_status' => $course->is_active,
                    'new_status' => $validated['is_active'],
                    'changed_by' => auth()->id()
                ]
            );
        }

        $course->update($validated);

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        // Log course deletion (before actual deletion)
        AuditLogService::logCustom(
            'course_deleted',
            "Course '{$course->title}' (Code: {$course->course_code}) was deleted",
            'courses',
            'warning',
            [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'course_code' => $course->course_code,
                'had_enrollments' => $course->enrollments()->count(),
                'deleted_by' => auth()->id()
            ]
        );

        $course->delete();

        return redirect()->route('courses.index')
                        ->with('success', 'Course deleted successfully.');
    }

    public function planning(): View
    {
        $courses = Course::active()->with('companyAssignments.company')->get();
        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();

        // Get all active companies for assignment dropdown
        $companies = Company::where('active', true)->orderBy('name')->get();

        // Group courses by category for better organization
        $coursesByCategory = $courses->groupBy('category');

        return view('courses.planning', compact('courses', 'coursesByCategory', 'categories', 'levels', 'deliveryMethods', 'companies'));
    }

    public function schedule(Course $course): View
    {
        $events = $course->courseEvents()
                         ->orderBy('start_date', 'asc')
                         ->orderBy('start_time', 'asc')
                         ->get();

        return view('courses.schedule', compact('course', 'events'));
    }

    public function showBulkInvite(Course $course): View
    {
        $this->authorize('update', $course);

        // Get all active companies
        $companies = Company::where('active', true)->orderBy('name')->get();

        // Get all teachers
        $teachers = User::role('teacher')->orderBy('name')->get();

        // Get all company managers
        $companyManagers = User::role('company_manager')->orderBy('name')->get();

        // Get companies assigned to this course
        $assignedCompanyIds = $course->assignedCompanies()->pluck('companies.id')->toArray();

        return view('courses.bulk-invite', compact('course', 'companies', 'teachers', 'companyManagers', 'assignedCompanyIds'));
    }

    public function sendBulkInvite(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'recipient_type' => 'required|in:companies,teachers,individual_users',
            'company_ids' => 'required_if:recipient_type,companies|array',
            'company_ids.*' => 'exists:companies,id',
            'teacher_ids' => 'required_if:recipient_type,teachers|array',
            'teacher_ids.*' => 'exists:users,id',
            'user_ids' => 'required_if:recipient_type,individual_users|array',
            'user_ids.*' => 'exists:users,id',
            'generate_temp_password' => 'boolean',
            'message' => 'nullable|string|max:1000',
        ]);

        $sentCount = 0;
        $recipientUsers = [];

        // Collect users based on recipient type
        if ($validated['recipient_type'] === 'companies' && !empty($validated['company_ids'])) {
            foreach ($validated['company_ids'] as $companyId) {
                $company = Company::find($companyId);
                $companyUsers = $company->users()->get();
                $recipientUsers = array_merge($recipientUsers, $companyUsers->all());

                // Also check if course-company assignment exists, if not create it
                $assignment = $course->companyAssignments()
                    ->where('company_id', $companyId)
                    ->first();

                if (!$assignment) {
                    $assignment = $course->companyAssignments()->create([
                        'company_id' => $companyId,
                        'assigned_by' => auth()->id(),
                        'assigned_date' => now(),
                        'is_mandatory' => false,
                    ]);
                }
            }
        } elseif ($validated['recipient_type'] === 'teachers' && !empty($validated['teacher_ids'])) {
            $recipientUsers = User::whereIn('id', $validated['teacher_ids'])->get()->all();
        } elseif ($validated['recipient_type'] === 'individual_users' && !empty($validated['user_ids'])) {
            $recipientUsers = User::whereIn('id', $validated['user_ids'])->get()->all();
        }

        // Remove duplicates
        $recipientUsers = collect($recipientUsers)->unique('id');

        // Send notifications to each user
        foreach ($recipientUsers as $user) {
            $tempPassword = null;

            // Generate temporary password if requested and user doesn't have a password
            if ($validated['generate_temp_password'] ?? false) {
                $tempPassword = \Str::random(12);
                $user->update([
                    'password' => bcrypt($tempPassword),
                    'must_change_password' => true,
                ]);
            }

            // Determine which notification to send
            if ($validated['recipient_type'] === 'companies') {
                // For company invitations, use CourseAssignedNotification
                $company = Company::whereHas('users', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->first();

                $assignment = $course->companyAssignments()
                    ->where('company_id', $company->id)
                    ->first();

                $user->notify(new \App\Notifications\CourseAssignedNotification($course, $assignment, $tempPassword));
            } else {
                // For individual invitations, check if enrollment exists
                $enrollment = $course->enrollments()->where('user_id', $user->id)->first();

                if (!$enrollment) {
                    // Create enrollment
                    $enrollment = $course->enrollments()->create([
                        'user_id' => $user->id,
                        'company_id' => $user->primary_company?->id,
                        'status' => 'enrolled',
                        'enrolled_at' => now(),
                        'progress_percentage' => 0,
                    ]);
                }

                $user->notify(new \App\Notifications\CourseEnrollmentNotification($course, $enrollment, $tempPassword));
            }

            $sentCount++;
        }

        // Log the bulk invitation
        AuditLogService::logCustom(
            'course_bulk_invitation_sent',
            "Sent bulk invitation for course '{$course->title}' to {$sentCount} user(s)",
            'courses',
            'info',
            [
                'course_id' => $course->id,
                'recipient_type' => $validated['recipient_type'],
                'recipient_count' => $sentCount,
                'temp_password_generated' => $validated['generate_temp_password'] ?? false,
                'sent_by' => auth()->id(),
            ]
        );

        return redirect()->route('courses.show', $course)
            ->with('success', "Invitations sent successfully to {$sentCount} user(s).");
    }
}
