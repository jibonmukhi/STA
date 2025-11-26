<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseEvent;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use Carbon\Carbon;

class EndUserDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $userCompanies = $user->companies;
        $primaryCompany = $user->primary_company;

        $stats = [
            'my_companies' => $userCompanies->count(),
            'total_percentage' => $user->total_percentage,
            'profile_completion' => $this->calculateProfileCompletion($user),
        ];

        return view('dashboards.end-user', compact('stats', 'userCompanies', 'primaryCompany'));
    }

    private function calculateProfileCompletion($user): int
    {
        $fields = ['name', 'surname', 'email', 'phone', 'date_of_birth', 'address'];
        $completed = 0;

        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }

    public function certificate(): View
    {
        return view('user.certificate');
    }

    public function calendar(Request $request): View
    {
        $user = Auth::user();

        // Use Italian timezone
        $italianTime = now()->setTimezone('Europe/Rome');

        $currentMonth = (int) $request->get('month', $italianTime->month);
        $currentYear = (int) $request->get('year', $italianTime->year);

        // Validate month and year parameters
        if ($currentMonth < 1 || $currentMonth > 12) {
            $currentMonth = $italianTime->month;
        }
        if ($currentYear < 1900 || $currentYear > 2100) {
            $currentYear = $italianTime->year;
        }

        // Get course schedules based on user role
        $coursesQuery = Course::query()
            ->instances() // Only course instances, not master templates
            ->with(['teachers', 'enrollments', 'assignedCompanies'])
            ->whereNotNull('start_date');

        if ($user->hasRole('teacher')) {
            // Teachers see courses they're assigned to
            $coursesQuery->whereHas('teachers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        } elseif ($user->hasRole('company_manager')) {
            // Company managers see courses assigned to their companies
            $companyIds = $user->companies()->pluck('companies.id')->toArray();
            $coursesQuery->where(function($q) use ($user, $companyIds) {
                $q->whereHas('enrollments', function($eq) use ($user) {
                    $eq->where('user_id', $user->id);
                })
                ->orWhereHas('assignedCompanies', function($cq) use ($companyIds) {
                    $cq->whereIn('companies.id', $companyIds);
                });
            });
        } elseif ($user->hasRole('end_user')) {
            // End users see only courses they're enrolled in
            $coursesQuery->whereHas('enrollments', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole(['sta_manager', 'admin'])) {
            // STA managers and admins see all courses (no additional filtering)
            // No query modifications needed - they see everything
        }

        // Get course IDs that match the user's permissions
        $courseIds = (clone $coursesQuery)->pluck('id')->toArray();

        // Get course sessions for current month
        $sessions = CourseSession::query()
            ->with(['course.teachers', 'course.enrollments', 'course.assignedCompanies'])
            ->whereIn('course_id', $courseIds)
            ->where(function($q) use ($currentYear, $currentMonth) {
                $q->whereYear('session_date', $currentYear)
                  ->whereMonth('session_date', $currentMonth);
            })
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Get today's sessions using Italian timezone
        $todaysSessions = CourseSession::query()
            ->with(['course.teachers', 'course.enrollments', 'course.assignedCompanies'])
            ->whereIn('course_id', $courseIds)
            ->whereDate('session_date', $italianTime->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        // Get upcoming sessions (next 7 days) using Italian timezone
        $upcomingSessions = CourseSession::query()
            ->with(['course.teachers', 'course.enrollments', 'course.assignedCompanies'])
            ->whereIn('course_id', $courseIds)
            ->whereDate('session_date', '>=', $italianTime->toDateString())
            ->whereDate('session_date', '<=', $italianTime->copy()->addDays(7)->toDateString())
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(10)
            ->get();

        // Convert sessions to events
        $events = $sessions;
        $todaysEvents = $todaysSessions;
        $upcomingEvents = $upcomingSessions;

        // Format events for JavaScript (convert CourseSession to event format)
        $formattedEvents = $sessions->map(function($session) {
            $course = $session->course;
            $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
            $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';

            // Get company names
            $companyNames = $course->assignedCompanies->pluck('name')->join(', ');

            return [
                'id' => $session->id,
                'sessionId' => $session->id,
                'courseId' => $course->id,
                'title' => $course->title . ' - ' . $session->session_title,
                'sessionTitle' => $session->session_title,
                'description' => $session->description ?? $course->description ?? '',
                'date' => $session->session_date->format('Y-m-d'),
                'startDate' => $session->session_date->format('Y-m-d'),
                'endDate' => $session->session_date->format('Y-m-d'),
                'startTime' => Carbon::parse($session->start_time)->format('H:i'),
                'endTime' => Carbon::parse($session->end_time)->format('H:i'),
                'status' => $session->status,
                'sessionStatus' => $session->status,
                'courseStatus' => $course->status,
                'location' => $session->location ?? ($course->delivery_method == 'online' ? 'Online' : ($course->delivery_method == 'offline' ? 'In Presenza' : 'Ibrido')),
                'courseTitle' => $course->title,
                'courseCode' => $course->course_code,
                'instructor' => $course->teachers->pluck('full_name')->join(', ') ?: ($course->instructor ?? 'N/A'),
                'companyNames' => $companyNames ?: 'N/A',
                'maxParticipants' => $session->max_participants ?? $course->max_participants ?? 0,
                'registeredParticipants' => $course->enrollments->count(),
                'category' => $course->category,
                'categoryColor' => $categoryColor,
                'statusColor' => $statusColor,
                'deliveryMethod' => $course->delivery_method,
                'durationHours' => $session->duration_hours,
                'eventType' => 'course'
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
            'total_events' => $sessions->count(),
            'todays_events' => $todaysSessions->count(),
            'upcoming_events' => $upcomingSessions->count(),
            'completed_events' => CourseSession::whereIn('course_id', $courseIds)->where('status', 'completed')->count(),
        ];

        // Return role-specific calendar view
        if ($user->hasRole('sta_manager')) {
            return view('sta.calendar', compact(
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
        } elseif ($user->hasRole('company_manager')) {
            return view('company.calendar', compact(
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
        } else {
            // Default for end users
            return view('user.calendar', compact(
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
    }

    public function reports(): View
    {
        $user = Auth::user();
        $userCompanies = $user->companies;

        // Generate some sample report data
        $reportData = [
            'monthly_summary' => [
                'total_companies' => $userCompanies->count(),
                'total_ownership' => $user->total_percentage,
                'active_since' => $user->created_at->format('F Y'),
                'profile_completion' => $this->calculateProfileCompletion($user)
            ],
            'company_breakdown' => $userCompanies->map(function($company) {
                return [
                    'name' => $company->name,
                    'role' => $company->pivot->role_in_company ?? 'Member',
                    'percentage' => $company->pivot->percentage ?? 0,
                    'joined_date' => $company->pivot->joined_at ? \Carbon\Carbon::parse($company->pivot->joined_at)->format('M Y') : 'N/A',
                    'is_primary' => $company->pivot->is_primary
                ];
            }),
            'activity_summary' => [
                'last_login' => $user->updated_at->diffForHumans(),
                'account_status' => ucfirst($user->status),
                'total_companies_joined' => $userCompanies->count(),
                'primary_company' => $user->primary_company->name ?? 'None'
            ]
        ];

        return view('user.reports', compact('reportData'));
    }

    /**
     * Display user's enrolled courses
     */
    public function myCourses(Request $request): View
    {
        $user = Auth::user();

        // Get user's enrolled courses with relationships
        $query = CourseEnrollment::where('user_id', $user->id)
            ->with(['course.teachers', 'course.assignedCompanies', 'course.sessions']);

        // Always exclude inactive courses - end users should NEVER see them
        $query->whereHas('course', function($q) {
            $q->where('status', '!=', 'inactive');
        });

        // Filter by course status
        if ($request->filled('status') && $request->status !== '') {
            // Show specific selected status (active, ongoing, or done)
            $query->whereHas('course', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        } else {
            // By default (no filter), show only active and ongoing courses
            $query->whereHas('course', function($q) {
                $q->whereIn('status', ['active', 'ongoing']);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('course', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $perPage = $request->get('per_page', 12);
        $enrollments = $query->orderBy('enrolled_at', 'desc')->paginate($perPage);

        // Get categories for filter
        $categories = Course::getCategories();

        // Calculate stats
        $stats = [
            'total_enrolled' => CourseEnrollment::where('user_id', $user->id)->count(),
            'in_progress' => CourseEnrollment::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->count(),
            'completed' => CourseEnrollment::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'average_progress' => CourseEnrollment::where('user_id', $user->id)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->avg('progress_percentage') ?? 0,
        ];

        return view('user.my-courses', compact('enrollments', 'categories', 'stats'));
    }

    /**
     * Display course details for enrolled user
     */
    public function showCourse(Course $course): View
    {
        $user = Auth::user();

        // Check if user is enrolled in this course
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'Non sei iscritto a questo corso.');
        }

        // Load relationships
        $course->load([
            'teachers',
            'assignedCompanies',
            'sessions' => function($query) {
                $query->orderBy('session_date', 'asc')->orderBy('start_time', 'asc');
            },
            'materials' => function($query) {
                $query->orderBy('order', 'asc')->orderBy('created_at', 'asc');
            }
        ]);

        return view('user.course-details', compact('course', 'enrollment'));
    }
}
