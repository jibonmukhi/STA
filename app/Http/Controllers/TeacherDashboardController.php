<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseEvent;
use App\Models\CourseEnrollment;
use App\Models\Certificate;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Get teacher's courses
        $myCourses = $user->teacherCourses()->with('enrollments')->get();

        // Calculate stats
        $stats = [
            'total_courses' => $myCourses->count(),
            'active_courses' => $myCourses->where('is_active', true)->count(),
            'total_students' => CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->distinct('user_id')
                ->count(),
            'completed_students' => CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
                ->where('status', 'completed')
                ->count(),
            'certificates_issued' => Certificate::whereIn('user_id',
                CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
                    ->pluck('user_id')
            )->count(),
        ];

        // Get upcoming course events
        $upcomingEvents = CourseEvent::whereIn('course_id', $myCourses->pluck('id'))
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        // Get recent enrollments
        $recentEnrollments = CourseEnrollment::whereIn('course_id', $myCourses->pluck('id'))
            ->with(['user', 'course'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboards.teacher', compact(
            'stats',
            'myCourses',
            'upcomingEvents',
            'recentEnrollments'
        ));
    }

    public function myCourses(Request $request): View
    {
        $user = Auth::user();

        $query = $user->teacherCourses()->with(['enrollments', 'courseEvents']);

        // Apply filters
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('show_inactive')) {
            // Show all courses including inactive
        } else {
            $query->active();
        }

        $courses = $query->orderBy('title')->paginate(12);

        $categories = Course::getCategories();
        $levels = Course::getLevels();

        return view('teacher.my-courses', compact('courses', 'categories', 'levels'));
    }

    public function courseStudents(Request $request, Course $course): View
    {
        $this->authorize('manageStudents', $course);

        $user = Auth::user();

        // Ensure this is teacher's course
        if ($course->teacher_id !== $user->id) {
            abort(403, 'Unauthorized access to this course.');
        }

        $query = CourseEnrollment::where('course_id', $course->id)
            ->with(['user', 'company']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('enrolled_at', 'desc')->paginate(20);

        $stats = [
            'total_enrolled' => CourseEnrollment::where('course_id', $course->id)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->count(),
            'completed' => CourseEnrollment::where('course_id', $course->id)
                ->where('status', 'completed')
                ->count(),
            'in_progress' => CourseEnrollment::where('course_id', $course->id)
                ->where('status', 'in_progress')
                ->count(),
            'average_progress' => CourseEnrollment::where('course_id', $course->id)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->avg('progress_percentage') ?? 0,
        ];

        return view('teacher.course-students', compact('course', 'enrollments', 'stats'));
    }

    public function schedule(Request $request): View
    {
        $user = Auth::user();
        $currentMonth = (int) $request->get('month', now()->month);
        $currentYear = (int) $request->get('year', now()->year);

        // Validate month and year parameters
        if ($currentMonth < 1 || $currentMonth > 12) {
            $currentMonth = now()->month;
        }
        if ($currentYear < 1900 || $currentYear > 2100) {
            $currentYear = now()->year;
        }

        // Get course events for teacher's courses
        $courseIds = $user->teacherCourses()->pluck('id')->toArray();

        $query = CourseEvent::with(['course', 'user', 'company'])
            ->whereIn('course_id', $courseIds);

        // Get events for current month
        $events = $query->byMonth($currentYear, $currentMonth)
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Get today's events
        $todaysEvents = CourseEvent::whereIn('course_id', $courseIds)
            ->whereDate('start_date', now()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        // Get upcoming events (next 7 days)
        $upcomingEvents = CourseEvent::whereIn('course_id', $courseIds)
            ->upcoming()
            ->whereDate('start_date', '>=', now()->toDateString())
            ->whereDate('start_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        // Format events for JavaScript
        $formattedEvents = $events->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description ?? '',
                'date' => $event->start_date->format('Y-m-d'),
                'startTime' => $event->start_time,
                'endTime' => $event->end_time,
                'status' => $event->status,
                'location' => $event->location ?? '',
                'courseTitle' => $event->course->title ?? '',
                'courseCode' => $event->course->course_code ?? '',
                'maxParticipants' => $event->max_participants,
                'registeredParticipants' => $event->registered_participants
            ];
        });

        // Localized month and day names for JavaScript
        $monthNames = [
            trans('calendar.months.january'),
            trans('calendar.months.february'),
            trans('calendar.months.march'),
            trans('calendar.months.april'),
            trans('calendar.months.may'),
            trans('calendar.months.june'),
            trans('calendar.months.july'),
            trans('calendar.months.august'),
            trans('calendar.months.september'),
            trans('calendar.months.october'),
            trans('calendar.months.november'),
            trans('calendar.months.december')
        ];

        $dayNames = [
            trans('calendar.days.sunday'),
            trans('calendar.days.monday'),
            trans('calendar.days.tuesday'),
            trans('calendar.days.wednesday'),
            trans('calendar.days.thursday'),
            trans('calendar.days.friday'),
            trans('calendar.days.saturday')
        ];

        $dayNamesShort = [
            trans('calendar.days_short.sun'),
            trans('calendar.days_short.mon'),
            trans('calendar.days_short.tue'),
            trans('calendar.days_short.wed'),
            trans('calendar.days_short.thu'),
            trans('calendar.days_short.fri'),
            trans('calendar.days_short.sat')
        ];

        // Stats for the calendar sidebar
        $stats = [
            'total_events' => $events->count(),
            'todays_events' => $todaysEvents->count(),
            'upcoming_events' => $upcomingEvents->count(),
            'completed_events' => CourseEvent::whereIn('course_id', $courseIds)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('teacher.schedule', compact(
            'events',
            'formattedEvents',
            'todaysEvents',
            'upcomingEvents',
            'stats',
            'currentMonth',
            'currentYear',
            'monthNames',
            'dayNames',
            'dayNamesShort'
        ));
    }

    public function certificates(): View
    {
        $user = Auth::user();

        // Get all students from teacher's courses
        $studentIds = CourseEnrollment::whereIn('course_id', $user->teacherCourses()->pluck('id'))
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // Get certificates for these students
        $certificates = Certificate::whereIn('user_id', $studentIds)
            ->orWhere('user_id', $user->id) // Include teacher's own certificates
            ->with(['user', 'company'])
            ->latest()
            ->paginate(20);

        return view('teacher.certificates', compact('certificates'));
    }
}
