<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Device;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{
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
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'master_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifier les identifiants de connexion
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->master_password])) {
            // Enregistrer l'échec dans les logs
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

            return response()->json(['message' => 'Ces identifiants ne correspondent pas à nos enregistrements.'], 401);
        }

        $user = Auth::user();

        // Vérifier si le compte est actif
        if ($user->status !== 'active') {
            Auth::logout();
            return response()->json(['message' => 'Votre compte n\'est pas actif. Veuillez vérifier votre email.'], 403);
        }

        // Vérifier si l'authentification à deux facteurs est activée
        if ($user->mfa_enabled) {
            // Stocker l'ID de l'utilisateur dans la session pour la vérification MFA
            session(['pending_user_id' => $user->id]);

            return response()->json([
                'message' => 'Veuillez entrer votre code d\'authentification à deux facteurs.',
                'requires_mfa' => true
            ]);
        }

        return $this->completeLogin($user, $request);
    }


    public function completeLogin(User $user, Request $request)
    {
        // Vérifier si l'appareil est déjà enregistré
        $deviceExists = Device::where('user_id', $user->id)
            ->where('ip_address', $request->ip())
            ->where('user_agent', $request->userAgent())
            ->exists();

        if (!$deviceExists) {
            // Si c'est un nouvel appareil, enregistrer l'appareil
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

            // Si le compte a l'option de notification des nouveaux appareils
            // Envoyer une notification à l'utilisateur
        }

        // Mettre à jour la dernière connexion
        $user->last_login = now();
        $user->save();

        // Enregistrer l'action dans les logs
        AccessLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'action' => 'login',
            'details' => 'Successful login',
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'status' => 'success'
        ]);

        // Créer le token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mfa_enabled' => $user->mfa_enabled,
            ]
        ]);
    }

    private function determineDeviceType() {}
}
