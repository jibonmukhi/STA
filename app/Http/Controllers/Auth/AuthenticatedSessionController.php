<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Role-based redirect
        $user = Auth::user();

        // Log successful login
        AuditLogService::logLogin($user);

        if ($user->hasRole('sta_manager')) {
            return redirect()->intended(route('sta.dashboard', absolute: false));
        } elseif ($user->hasRole('company_manager')) {
            return redirect()->intended(route('company.dashboard', absolute: false));
        } elseif ($user->hasRole('teacher')) {
            return redirect()->intended(route('teacher.dashboard', absolute: false));
        } elseif ($user->hasRole('end_user')) {
            return redirect()->intended(route('user.dashboard', absolute: false));
        }

        // Default fallback
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log logout before destroying session
        $user = Auth::user();
        if ($user) {
            AuditLogService::logLogout($user);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
