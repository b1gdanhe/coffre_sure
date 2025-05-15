<?php

namespace App\Services;

use App\Models\User;
use App\Mail\MfaCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EmailMfaService
{
    /**
     * Génère et envoie un code MFA par email.
     *
     * @param User $user
     * @return string Le code généré
     */
    public function sendMfaCode(User $user)
    {
        $code = $this->generateMfaCode();
        $expirationMinutes = config('auth.mfa.email_expiration', 15);
        $cacheKey = "mfa_code_{$user->id}";
        Cache::put($cacheKey, $code, now()->addMinutes($expirationMinutes));
        $expiresAt = now()->addMinutes($expirationMinutes);
        Mail::to($user->email)->send(new MfaCodeMail($user, $code, $expiresAt));

        return $code;
    }

    /**
     * Valide un code MFA pour un utilisateur donné.
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function validateMfaCode(User $user, string $code)
    {
        $cacheKey = "mfa_code_{$user->id}";

        // Récupérer le code stocké en cache
        $storedCode = Cache::get($cacheKey);

        if (!$storedCode) {
            return false; // Code expiré ou jamais généré
        }

        // Validation du code
        $isValid = $storedCode === $code;

        if ($isValid) {
            Cache::forget($cacheKey);
        }

        return $isValid;
    }

    /**
     * Génère un code MFA aléatoire.
     *
     * @return string
     */
    protected function generateMfaCode()
    {
        return sprintf('%06d', mt_rand(0, 999999));
    }
}
