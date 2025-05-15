<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class  MfaController extends Controller
{
    protected  $loginController;

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
        if (!$this->validateMfaCode($user, $request->code)) {
            return back()->withErrors(['code' => 'Code d\'authentification invalide.']);
        }

        // Authentifier l'utilisateur manuellement
        Auth::login($user);
        session()->forget('pending_user_id');

        return $this->loginController->completeLogin($user, $request);
    }

    protected function validateMfaCode(User $user, $code)
    {
        // Implémentez ici la validation du code MFA
        // (Google Authenticator, SMS, Email, etc.)

        // Exemple avec Google Authenticator:
        // return app(Google2FA::class)->verifyKey($user->two_factor_secret, $code);

        // Retour temporaire pour l'exemple
        return $code === '123456';
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
