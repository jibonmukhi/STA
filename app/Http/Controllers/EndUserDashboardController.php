<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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

    public function calendar(): View
    {
        return view('user.calendar');
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
