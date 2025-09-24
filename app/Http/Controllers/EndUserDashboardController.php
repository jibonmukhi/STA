<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseEvent;
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

        // Get course events for the user based on their role
        $query = CourseEvent::with(['user', 'company']);

        if ($user->hasRole('end_user')) {
            // End users see only their own events
            $query->forUser($user->id);
        } elseif ($user->hasRole('company_manager')) {
            // Company managers see events from their companies and their own events
            $companyIds = $user->companies()->pluck('companies.id')->toArray();
            $query->where(function($q) use ($user, $companyIds) {
                $q->forUser($user->id)
                  ->orWhereIn('company_id', $companyIds);
            });
        }
        // STA managers see all events (no additional filtering needed)

        // Get events for current month
        $events = $query->byMonth($currentYear, $currentMonth)
                       ->orderBy('start_date', 'asc')
                       ->orderBy('start_time', 'asc')
                       ->get();

        // Get today's events
        $todaysEvents = $query->whereDate('start_date', now()->toDateString())
                             ->orderBy('start_time', 'asc')
                             ->get();

        // Get upcoming events (next 7 days)
        $upcomingEvents = $query->upcoming()
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
                'instructor' => $event->course->instructor ?? '',
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
            'completed_events' => $query->where('status', 'completed')->count(),
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
