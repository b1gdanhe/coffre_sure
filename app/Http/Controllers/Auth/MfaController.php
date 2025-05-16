<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\LoginService;
use App\Services\EmailMfaService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class  MfaController extends Controller
{
    protected  $loginController;
    private EmailMfaService $emailMfaService;
    private LoginService $loginService;


    public function __construct(EmailMfaService $emailMfaService, LoginService $loginService)
    {
        $this->emailMfaService = $emailMfaService;
        $this->loginService = $loginService;
    }

    public function sendMfaCode()
    {
        if (!session()->has('pending_user_id')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expirée. Veuillez vous reconnecter.']);
        }

        return Inertia::render('auth/SendMfaCode');
    }
    // MfaController.php
    public function verifyMfa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('pending_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expirée. Veuillez vous reconnecter.']);
        }

        $user = User::findOrFail($userId);

        // Vérifier le code MFA (à implémenter selon votre solution 2FA)
        if (!$this->emailMfaService->validateMfaCode($user, $request->code)) {
            return back()->withErrors(['code' => 'Code d\'authentification invalide.']);
        }

        // Authentifier l'utilisateur manuellement
        Auth::login($user);
        session()->forget('pending_user_id');

        return $this->loginService->completeLogin($user, $request);
    }

    public function resendMfaCode(Request $request)
    {
        $userId = session('pending_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expirée. Veuillez vous reconnecter.']);
        }

        $user = User::findOrFail($userId);

        // Envoyer un nouveau code
        $this->sendMfaCode($user);

        return back()->with('status', 'Un nouveau code d\'authentification a été envoyé à votre adresse email.');
    }
}
