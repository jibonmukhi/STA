<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\AuditLogService;

class CourseManagementController extends Controller
{
    /**
     * Display all course instances (started courses)
     */
    public function index(Request $request): View
    {
        $query = Course::query()->instances();

        // Filter by company if user is company manager
        if (auth()->user()->hasRole('company_manager')) {
            $userCompanyIds = auth()->user()->companies->pluck('id');
            $query->whereHas('assignedCompanies', function($q) use ($userCompanyIds) {
                $q->whereIn('companies.id', $userCompanyIds);
            });
        }

        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        if ($request->has('level') && $request->level) {
            $query->byLevel($request->level);
        }

        if ($request->has('delivery_method') && $request->delivery_method) {
            $query->where('delivery_method', $request->delivery_method);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->has('company_id') && $request->company_id) {
            $query->whereHas('assignedCompanies', function($q) use ($request) {
                $q->where('companies.id', $request->company_id);
            });
        }

        // Filter by teacher
        if ($request->has('teacher_id') && $request->teacher_id) {
            $query->whereHas('teachers', function($q) use ($request) {
                $q->where('users.id', $request->teacher_id);
            });
        }

        // Filter by start date
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        // Filter by end date
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('end_date', '<=', $request->end_date);
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

        $perPage = $request->input('per_page', 25);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        $courses = $query->with(['teacher', 'teachers', 'parentCourse', 'assignedCompanies'])->orderBy('created_at', 'desc')->paginate($perPage);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();
        $statuses = Course::getStatuses();

        // Get all companies for filter dropdown
        $companies = Company::where('active', true)->orderBy('name')->get();

        // Get all teachers for filter dropdown
        $teachers = User::role('teacher')->orderBy('name')->get();

        return view('course-management.index', compact('courses', 'categories', 'levels', 'deliveryMethods', 'statuses', 'companies', 'teachers'));
    }

    /**
     * Show form to create new course instance
     */
    public function create(): View
    {
        $this->authorize('create', Course::class);

        // Get all master courses (templates)
        $masterCourses = Course::query()->masters()->active()->orderBy('title')->get();

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();
        $statuses = Course::getStatuses();

        // Get users with teacher role
        $teachers = User::role('teacher')->orderBy('name')->get();

        // Get all active companies
        $companies = Company::where('active', true)->orderBy('name')->get();

        // Get all users (potential students) with their companies
        // Exclude users with admin, super_admin, or teacher roles
        $users = User::with('companies')
            ->whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['admin', 'super_admin', 'teacher']);
            })
            ->orderBy('name')
            ->get();

        return view('course-management.create', compact('masterCourses', 'categories', 'levels', 'deliveryMethods', 'statuses', 'teachers', 'companies', 'users'));
    }

    /**
     * Store a new course instance
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'parent_course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'category' => 'required|string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'credits' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'instructor' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
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

        // Set default values for optional fields
        $validated['price'] = $validated['price'] ?? 0;
        $validated['credits'] = $validated['credits'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_mandatory'] = $validated['is_mandatory'] ?? false;

        $course = Course::create($validated);

        // Sync multiple teachers
        if (!empty($validated['teacher_ids'])) {
            $course->teachers()->sync($validated['teacher_ids']);
        }

        // Assign company if selected
        if (!empty($validated['company_id'])) {
            $course->companyAssignments()->create([
                'company_id' => $validated['company_id'],
                'assigned_by' => auth()->id(),
                'assigned_date' => now(),
                'is_mandatory' => false,
            ]);
        }

        // Enroll students if selected
        if (!empty($validated['student_ids'])) {
            // Use course's assigned company to ensure consistency
            $courseCompanyId = !empty($validated['company_id']) ? $validated['company_id'] : null;

            foreach ($validated['student_ids'] as $studentId) {
                $student = User::find($studentId);

                // Priority: course's assigned company > user's primary company
                $companyId = $courseCompanyId ?: $student->primary_company?->id;

                $course->enrollments()->create([
                    'user_id' => $studentId,
                    'company_id' => $companyId,
                    'status' => 'enrolled',
                    'enrolled_at' => now(),
                    'progress_percentage' => 0,
                ]);
            }
        }

        AuditLogService::log(
            'Course Instance Created',
            $course,
            null,
            $course->toArray(),
            'Created course instance: ' . $course->title . ' (Code: ' . $course->course_code . ')',
            'Course Management'
        );

        return redirect()->route('course-management.show', $course)
                        ->with('success', 'Course instance created successfully.');
    }

    /**
     * Display course instance details
     */
    public function show(Course $courseManagement): View
    {
        $courseManagement->load(['materials.uploader', 'teacher', 'teachers', 'parentCourse', 'assignedCompanies']);
        return view('course-management.show', ['course' => $courseManagement]);
    }

    /**
     * Show form to edit course instance
     */
    public function edit(Course $courseManagement): View
    {
        $this->authorize('update', $courseManagement);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();
        $statuses = Course::getStatuses();

        // Get users with teacher role
        $teachers = User::role('teacher')->orderBy('name')->get();

        // Get all users (potential students) with their companies
        // Exclude users with admin, super_admin, or teacher roles
        $users = User::with('companies')
            ->whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['admin', 'super_admin', 'teacher']);
            })
            ->orderBy('name')
            ->get();

        // Load enrollments for the course
        $courseManagement->load(['enrollments', 'assignedCompanies']);

        return view('course-management.edit', [
            'course' => $courseManagement,
            'categories' => $categories,
            'levels' => $levels,
            'deliveryMethods' => $deliveryMethods,
            'statuses' => $statuses,
            'teachers' => $teachers,
            'users' => $users
        ]);
    }

    /**
     * Update course instance
     */
    public function update(Request $request, Course $courseManagement): RedirectResponse
    {
        $this->authorize('update', $courseManagement);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $courseManagement->id,
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:users,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable',
        ]);

        // Log course status change if applicable
        if (isset($validated['is_active']) && $courseManagement->is_active !== $validated['is_active']) {
            $statusText = $validated['is_active'] ? 'activated' : 'deactivated';
            AuditLogService::logCustom(
                'course_instance_status_changed',
                "Course instance '{$courseManagement->title}' was {$statusText}",
                'courses',
                'info',
                [
                    'course_id' => $courseManagement->id,
                    'course_title' => $courseManagement->title,
                    'old_status' => $courseManagement->is_active,
                    'new_status' => $validated['is_active'],
                    'changed_by' => auth()->id()
                ]
            );
        }

        $courseManagement->update($validated);

        // Sync multiple teachers
        if (isset($validated['teacher_ids'])) {
            $courseManagement->teachers()->sync($validated['teacher_ids']);
        } else {
            // If no teachers selected, clear all
            $courseManagement->teachers()->sync([]);
        }

        // Sync student enrollments
        if (isset($validated['student_ids'])) {
            $currentEnrollmentIds = $courseManagement->enrollments->pluck('user_id')->toArray();
            $newStudentIds = $validated['student_ids'];

            // Remove students who were unchecked
            $toRemove = array_diff($currentEnrollmentIds, $newStudentIds);
            if (!empty($toRemove)) {
                $courseManagement->enrollments()->whereIn('user_id', $toRemove)->delete();
            }

            // Add new students who were checked
            $toAdd = array_diff($newStudentIds, $currentEnrollmentIds);
            foreach ($toAdd as $studentId) {
                $student = User::find($studentId);
                $primaryCompany = $student->primary_company;

                $courseManagement->enrollments()->create([
                    'user_id' => $studentId,
                    'company_id' => $primaryCompany ? $primaryCompany->id : null,
                    'status' => 'enrolled',
                    'enrolled_at' => now(),
                    'progress_percentage' => 0,
                ]);
            }
        } else {
            // If no students selected, remove all enrollments
            $courseManagement->enrollments()->delete();
        }

        return redirect()->route('course-management.show', $courseManagement)
                        ->with('success', 'Course instance updated successfully.');
    }

    /**
     * Delete course instance
     */
    public function destroy(Course $courseManagement): RedirectResponse
    {
        $this->authorize('delete', $courseManagement);

        // Log course deletion (before actual deletion)
        AuditLogService::logCustom(
            'course_instance_deleted',
            "Course instance '{$courseManagement->title}' (Code: {$courseManagement->course_code}) was deleted",
            'courses',
            'warning',
            [
                'course_id' => $courseManagement->id,
                'course_title' => $courseManagement->title,
                'course_code' => $courseManagement->course_code,
                'had_enrollments' => $courseManagement->enrollments()->count(),
                'deleted_by' => auth()->id()
            ]
        );

        $courseManagement->delete();

        return redirect()->route('course-management.index')
                        ->with('success', 'Course instance deleted successfully.');
    }

    /**
     * Show bulk invite form
     */
    public function showBulkInvite(Course $courseManagement): View
    {
        $this->authorize('update', $courseManagement);

        // Get all active companies
        $companies = Company::where('active', true)->orderBy('name')->get();

        // Get all teachers
        $teachers = User::role('teacher')->orderBy('name')->get();

        // Get all company managers
        $companyManagers = User::role('company_manager')->orderBy('name')->get();

        // Get companies assigned to this course instance
        $assignedCompanyIds = $courseManagement->assignedCompanies()->pluck('companies.id')->toArray();

        return view('course-management.bulk-invite', [
            'course' => $courseManagement,
            'companies' => $companies,
            'teachers' => $teachers,
            'companyManagers' => $companyManagers,
            'assignedCompanyIds' => $assignedCompanyIds
        ]);
    }

    /**
     * Send bulk invitations
     */
    public function sendBulkInvite(Request $request, Course $courseManagement): RedirectResponse
    {
        $this->authorize('update', $courseManagement);

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
                $assignment = $courseManagement->companyAssignments()
                    ->where('company_id', $companyId)
                    ->first();

                if (!$assignment) {
                    $assignment = $courseManagement->companyAssignments()->create([
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

                $assignment = $courseManagement->companyAssignments()
                    ->where('company_id', $company->id)
                    ->first();

                $user->notify(new \App\Notifications\CourseAssignedNotification($courseManagement, $assignment, $tempPassword));
            } else {
                // For individual invitations, check if enrollment exists
                $enrollment = $courseManagement->enrollments()->where('user_id', $user->id)->first();

                if (!$enrollment) {
                    // Create enrollment
                    $enrollment = $courseManagement->enrollments()->create([
                        'user_id' => $user->id,
                        'company_id' => $user->primary_company?->id,
                        'status' => 'enrolled',
                        'enrolled_at' => now(),
                        'progress_percentage' => 0,
                    ]);
                }

                $user->notify(new \App\Notifications\CourseEnrollmentNotification($courseManagement, $enrollment, $tempPassword));
            }

            $sentCount++;
        }

        // Log the bulk invitation
        AuditLogService::logCustom(
            'course_instance_bulk_invitation_sent',
            "Sent bulk invitation for course instance '{$courseManagement->title}' to {$sentCount} user(s)",
            'courses',
            'info',
            [
                'course_id' => $courseManagement->id,
                'recipient_type' => $validated['recipient_type'],
                'recipient_count' => $sentCount,
                'temp_password_generated' => $validated['generate_temp_password'] ?? false,
                'sent_by' => auth()->id(),
            ]
        );

        return redirect()->route('course-management.show', $courseManagement)
            ->with('success', "Invitations sent successfully to {$sentCount} user(s).");
    }
}
