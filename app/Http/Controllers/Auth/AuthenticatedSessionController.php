<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Device;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\EmailMfaService;
use App\Http\Controllers\Controller;
use App\Services\RedirectionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LoginService;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{

    private EmailMfaService $emailMfaService;
    private LoginService $loginService;



    public function __construct(EmailMfaService $emailMfaService, LoginService $loginService)
    {
        $this->emailMfaService = $emailMfaService;
        $this->loginService = $loginService;
    }

    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $user = Auth::user();
        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Votre compte n\'est pas actif. Veuillez vérifier votre email.',
            ])->withInput($request->except('password'));
        }
        if ($user->mfa_enabled) {
            session(['pending_user_id' => $user->id]);
            $this->emailMfaService->sendMfaCode($user);
            return redirect()->route('mfa.send-code');
        }

        return $this->loginService->completeLogin($user, $request);
    }
}
