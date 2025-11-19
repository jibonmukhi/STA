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
    public function index(Request $request): View|RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index');
        }

        // Only show master courses (templates)
        $query = Course::query()->masters();

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

        // Get per_page value from request, default to 25
        $perPage = $request->input('per_page', 25);

        // Validate per_page value (only allow specific values)
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        // Master courses don't need teacher relationships loaded (they're templates)
        $courses = $query->orderBy('title')->paginate($perPage);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();

        return view('courses.index', compact('courses', 'categories', 'levels', 'deliveryMethods'));
    }

    public function create(): View|RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to create master course templates.');
        }

        $this->authorize('create', Course::class);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();
        $statuses = Course::getStatuses();

        // Master courses don't have teachers - they're templates
        return view('courses.create', compact('categories', 'levels', 'deliveryMethods', 'statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to create master course templates.');
        }

        $this->authorize('create', Course::class);

        // Master courses are templates - no teachers, no dates
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'category' => 'required|string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'credits' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'prerequisites' => 'nullable|string',
            'delivery_method' => 'required|string|in:online,offline,hybrid',
            'is_active' => 'boolean',
            'status' => 'required|string|in:active,inactive,ongoing,done',
        ]);

        // Ensure parent_course_id is null for master courses
        $validated['parent_course_id'] = null;

        $course = Course::create($validated);

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Course created successfully.');
    }

    public function show(Request $request, Course $course): View|RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to view master course templates.');
        }

        // Load course materials
        $course->load(['materials.uploader']);

        // Paginate course instances
        $perPage = $request->input('per_page', 10);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $instances = $course->instances()
            ->with(['teachers', 'enrollments'])
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);

        return view('courses.show', compact('course', 'instances'));
    }

    public function edit(Course $course): View|RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to edit master course templates.');
        }

        $this->authorize('update', $course);

        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();
        $statuses = Course::getStatuses();

        // Master courses don't have teachers - they're templates
        return view('courses.edit', compact('course', 'categories', 'levels', 'deliveryMethods', 'statuses'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to edit master course templates.');
        }

        $this->authorize('update', $course);

        // Master courses are templates - no teachers, no dates
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $course->id,
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'category' => 'required|string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'credits' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'prerequisites' => 'nullable|string',
            'delivery_method' => 'required|string|in:online,offline,hybrid',
            'is_active' => 'boolean',
            'status' => 'required|string|in:active,inactive,ongoing,done',
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
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to delete master course templates.');
        }

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

    public function planning(): View|RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to view master course planning.');
        }

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

    public function schedule(Course $course): View|RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to view master course schedules.');
        }

        $events = $course->courseEvents()
                         ->orderBy('start_date', 'asc')
                         ->orderBy('start_time', 'asc')
                         ->get();

        return view('courses.schedule', compact('course', 'events'));
    }

    public function showBulkInvite(Course $course): View|RedirectResponse
    {
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to send bulk invites for master courses.');
        }

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
        // Redirect company managers to course-management instead
        if (auth()->user()->hasRole('company_manager') || auth()->user()->hasRole('end_user')) {
            return redirect()->route('course-management.index')
                           ->with('error', 'You do not have permission to send bulk invites for master courses.');
        }

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
