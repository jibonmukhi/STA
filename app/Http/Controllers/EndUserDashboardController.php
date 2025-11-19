<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseEvent;
use App\Models\Course;
use App\Models\CourseEnrollment;
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
        $currentMonth = (int) $request->get('month', now()->month);
        $currentYear = (int) $request->get('year', now()->year);

        // Validate month and year parameters
        if ($currentMonth < 1 || $currentMonth > 12) {
            $currentMonth = now()->month;
        }
        if ($currentYear < 1900 || $currentYear > 2100) {
            $currentYear = now()->year;
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
        }
        // STA managers see all courses (no additional filtering)

        // Get courses for current month
        $courses = (clone $coursesQuery)->where(function($q) use ($currentYear, $currentMonth) {
            $q->whereYear('start_date', $currentYear)
              ->whereMonth('start_date', $currentMonth);
        })->orderBy('start_date', 'asc')->get();

        // Get today's courses
        $todaysCourses = (clone $coursesQuery)->whereDate('start_date', now()->toDateString())->get();

        // Get upcoming courses (next 7 days)
        $upcomingCourses = (clone $coursesQuery)
            ->whereDate('start_date', '>=', now()->toDateString())
            ->whereDate('start_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        // Convert courses to event objects
        $events = $courses;
        $todaysEvents = $todaysCourses;
        $upcomingEvents = $upcomingCourses;

        // Format events for JavaScript (convert Course to event format)
        $formattedEvents = $events->map(function($course) {
            $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
            $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';

            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description ?? '',
                'date' => $course->start_date->format('Y-m-d'),
                'startDate' => $course->start_date->format('Y-m-d'),
                'endDate' => $course->end_date ? $course->end_date->format('Y-m-d') : $course->start_date->format('Y-m-d'),
                'startTime' => $course->start_time ?? '09:00',
                'endTime' => $course->end_time ?? '17:00',
                'status' => $course->status,
                'location' => $course->delivery_method == 'online' ? 'Online' : ($course->delivery_method == 'offline' ? 'In Presenza' : 'Ibrido'),
                'courseTitle' => $course->title,
                'courseCode' => $course->course_code,
                'instructor' => $course->teachers->pluck('full_name')->join(', ') ?: ($course->instructor ?? 'N/A'),
                'maxParticipants' => $course->max_participants ?? 0,
                'registeredParticipants' => $course->enrollments->count(),
                'category' => $course->category,
                'categoryColor' => $categoryColor,
                'statusColor' => $statusColor,
                'deliveryMethod' => $course->delivery_method,
                'durationHours' => $course->duration_hours
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
            'completed_events' => (clone $coursesQuery)->where('status', 'completed')->count(),
        ];

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
}
