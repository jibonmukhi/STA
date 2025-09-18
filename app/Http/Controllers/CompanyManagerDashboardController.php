<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CompanyManagerDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $userCompanies = $user->companies;

        $stats = [
            'my_companies' => $userCompanies->count(),
            'company_users' => $userCompanies->sum(function($company) {
                return $company->users->count();
            }),
            'active_company_users' => $userCompanies->sum(function($company) {
                return $company->users->where('status', 'active')->count();
            }),
        ];

        $companyUsers = collect();
        foreach ($userCompanies as $company) {
            $companyUsers = $companyUsers->merge($company->users);
        }
        $recentCompanyUsers = $companyUsers->sortByDesc('created_at')->take(5);

        return view('dashboards.company-manager', compact('stats', 'userCompanies', 'recentCompanyUsers'));
    }
}
