<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store2(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('user.dashboard');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Générer un sel (salt) unique
        $salt = bin2hex(random_bytes(16));

        // Dériver la clé de chiffrement à partir du mot de passe maître
        $masterKey = hash_pbkdf2('sha256', $request->password, $salt, 100000, 32, true);
        $masterKeyHash = hash('sha256', $masterKey);

        // Générer une clé de chiffrement pour les données de l'utilisateur
        $encryptionKey = bin2hex(random_bytes(32));

        // Chiffrer la clé d'encryption avec la clé maître
        $encryptedKey = openssl_encrypt(
            $encryptionKey,
            'aes-256-gcm',
            $masterKey,
            0,
            random_bytes(16),
            $tag
        );

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->master_password), // Pour l'authentification Laravel
            'master_key_hash' => $masterKeyHash, // Pour vérifier la dérivation de clé
            'salt' => $salt,
            'encryption_key' => $encryptedKey,
            'status' => 'pending'
        ]);

        // Envoyer l'email de vérification
        event(new Registered($user));

        Auth::login($user);

        AccessLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'details' => 'Initial registration',
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'status' => 'success'
        ]);

        return to_route('user.dashboard');
    }
}
