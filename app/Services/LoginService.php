<?php

namespace App\Services;

use App\Models\User;
use App\Models\Device;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginService
{

    private RedirectionService $redirectionService;

    public function __construct(RedirectionService $redirectionService)
    {
        $this->redirectionService = $redirectionService;
    }

    /**
     * Finalise le processus de connexion après authentification réussie.
     * 
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completeLogin(User $user, Request $request)
    {
        // Régénérer la session pour éviter les attaques de fixation de session
        $request->session()->regenerate();

        // Vérifier si l'appareil est déjà enregistré
        $deviceExists = Device::where('user_id', $user->id)
            ->where('ip_address', $request->ip())
            ->where('user_agent', $request->userAgent())
            ->exists();

        if (!$deviceExists) {
            // Si c'est un nouvel appareil, l'enregistrer
            $this->registerNewDevice($user, $request);
        }

        // Mettre à jour la dernière connexion
        $user->last_login = now();
        $user->save();

        // Enregistrer l'action dans les logs
        $this->logSuccessfulLogin($user, $request);

        // Rediriger l'utilisateur en fonction de son rôle
        return redirect()->intended(
            route($this->redirectionService->getRedirectRouteForUser($user), absolute: false)
        );
    }

    /**
     * Enregistre un nouvel appareil pour l'utilisateur.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function registerNewDevice(User $user, Request $request)
    {
        $device = new Device([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Appareil ' . now()->format('Y-m-d H:i'),
            'device_type' => $this->determineDeviceType($request->userAgent()),
            'auth_token' => bin2hex(random_bytes(32)),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        $device->save();

        // Si l'utilisateur a activé l'option, lui envoyer une notification
        if ($user->notify_on_new_device) {
            $user->notifyNewDeviceLogin($device);
        }
    }

    /**
     * Détermine le type d'appareil à partir de l'user-agent.
     *
     * @param  string  $userAgent
     * @return string
     */
    protected function determineDeviceType($userAgent)
    {
        // Logique simple pour déterminer le type d'appareil
        if (preg_match('/(tablet|ipad)/i', $userAgent)) {
            return 'tablet';
        } elseif (preg_match('/(mobile|iphone|android)/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/(chrome|firefox|safari|edge).*extension/i', $userAgent)) {
            return 'browser_extension';
        } elseif (preg_match('/(chrome|firefox|safari|edge|msie|trident)/i', $userAgent)) {
            return 'desktop';
        }

        return 'other';
    }

    /**
     * Enregistre une tentative de connexion échouée.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function logFailedAttempt(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            AccessLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'action' => 'failed_login',
                'details' => 'Invalid password',
                'ip_address' => $request->ip(),
                'device_info' => $request->userAgent(),
                'status' => 'failed'
            ]);
        }
    }

    /**
     * Enregistre une connexion réussie.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function logSuccessfulLogin(User $user, Request $request)
    {
        AccessLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'action' => 'login',
            'details' => 'Successful login',
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'status' => 'success'
        ]);
    }


    /**
     * Déconnecte l'utilisateur de l'application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
