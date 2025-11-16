<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\AuditLogService;
use App\Rules\ValidCodiceFiscale;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'cf' => [
                'required',
                'string',
                'size:16',
                'unique:users',
                'regex:/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/',
                new ValidCodiceFiscale([
                    'name' => $request->name,
                    'surname' => $request->surname,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                ])
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'date_of_birth' => $request->date_of_birth,
            'place_of_birth' => $request->place_of_birth,
            'gender' => $request->gender,
            'cf' => strtoupper($request->cf),
            'country' => 'IT', // Default to Italy for self-registration
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending_approval', // Require approval for self-registered users
        ]);

        // Log user registration
        AuditLogService::logCustom(
            'user_registered',
            "New user registered: {$user->name} ({$user->email})",
            'users',
            'info',
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'registration_method' => 'self_registration',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
