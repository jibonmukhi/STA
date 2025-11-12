<?php

namespace App\Http\Controllers;

use App\Models\Course;
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

        // Get users with teacher role
        $teachers = User::role('teacher')->orderBy('name')->get();

        return view('courses.create', compact('categories', 'levels', 'deliveryMethods', 'teachers'));
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
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
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

        // Get users with teacher role
        $teachers = User::role('teacher')->orderBy('name')->get();

        return view('courses.edit', compact('course', 'categories', 'levels', 'deliveryMethods', 'teachers'));
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
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
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
        $courses = Course::active()->get();
        $categories = Course::getCategories();
        $levels = Course::getLevels();
        $deliveryMethods = Course::getDeliveryMethods();

        // Group courses by category for better organization
        $coursesByCategory = $courses->groupBy('category');

        return view('courses.planning', compact('courses', 'coursesByCategory', 'categories', 'levels', 'deliveryMethods'));
    }

    public function schedule(Course $course): View
    {
        $events = $course->courseEvents()
                         ->orderBy('start_date', 'asc')
                         ->orderBy('start_time', 'asc')
                         ->get();

        return view('courses.schedule', compact('course', 'events'));
    }
}
