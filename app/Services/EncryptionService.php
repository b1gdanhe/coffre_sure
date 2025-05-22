<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EncryptionService
{
    /**
     * Generate a secure encryption key for a vault.
     */
    public function generateVaultKey(): string
    {
        // Générer une clé de 64 caractères pour AES-256
        return Hash::make(Str::random(64) . now()->timestamp . uniqid());
    }

    /**
     * Encrypt sensitive data for storage.
     */
    public function encryptSensitiveData(string $data, ?string $vaultKey = null): string
    {
        if ($vaultKey) {
            // Utiliser la clé du coffre pour un chiffrement spécifique
            return $this->encryptWithCustomKey($data, $vaultKey);
        }

        // Utiliser le chiffrement Laravel par défaut
        return Crypt::encryptString($data);
    }

    /**
     * Decrypt sensitive data.
     */
    public function decryptSensitiveData(string $encryptedData, ?string $vaultKey = null): string
    {
        if ($vaultKey) {
            return $this->decryptWithCustomKey($encryptedData, $vaultKey);
        }

        return Crypt::decryptString($encryptedData);
    }

    /**
     * Encrypt data with a custom key.
     */
    public function encryptWithCustomKey(string $key): string
    {
        $cipher = 'AES-256-CBC';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $hashedKey = hash('sha256', $key, true);
        $encrypted = openssl_encrypt($this->generateVaultKey(), $cipher, $hashedKey, OPENSSL_RAW_DATA, $iv);

        // Combine IV and encrypted data
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data with a custom key.
     */
    public function decryptWithCustomKey(string $encryptedData, string $key): string
    {
        $cipher = 'AES-256-CBC';
        $data = base64_decode($encryptedData);
        $ivLength = openssl_cipher_iv_length($cipher);

        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        $hashedKey = hash('sha256', $key, true);
        $decrypted = openssl_decrypt($encrypted, $cipher, $hashedKey, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            throw new \RuntimeException('Échec du déchiffrement des données');
        }

        return $decrypted;
    }

    /**
     * Generate a secure password.
     */
    public function generateSecurePassword(int $length = 16, bool $includeSymbols = true): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $chars = $lowercase . $uppercase . $numbers;

        if ($includeSymbols) {
            $chars .= $symbols;
        }

        $password = '';

        // Assurer au moins un caractère de chaque type
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];

        if ($includeSymbols) {
            $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        }

        // Compléter avec des caractères aléatoires
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // Mélanger les caractères
        return str_shuffle($password);
    }

    /**
     * Hash a master password for storage.
     */
    public function hashMasterPassword(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Verify a master password.
     */
    public function verifyMasterPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }

    /**
     * Generate a vault salt for additional security.
     */
    public function generateVaultSalt(): string
    {
        return Str::random(32);
    }

    /**
     * Create a derived key from user password and vault salt.
     */
    public function deriveKeyFromPassword(string $password, string $salt, int $iterations = 10000): string
    {
        return hash_pbkdf2('sha256', $password, $salt, $iterations, 32, true);
    }

    /**
     * Encrypt vault metadata (non-sensitive).
     */
    public function encryptVaultMetadata(array $metadata): string
    {
        return Crypt::encryptString(json_encode($metadata));
    }

    /**
     * Decrypt vault metadata.
     */
    public function decryptVaultMetadata(string $encryptedMetadata): array
    {
        $decrypted = Crypt::decryptString($encryptedMetadata);
        return json_decode($decrypted, true) ?? [];
    }

    /**
     * Generate a secure token for sharing vaults.
     */
    public function generateShareToken(): string
    {
        return Str::random(64);
    }

    /**
     * Validate encryption key strength.
     */
    public function validateKeyStrength(string $key): bool
    {
        // Vérifier la longueur minimale
        if (strlen($key) < 32) {
            return false;
        }

        // Vérifier la complexité (au moins 3 types de caractères)
        $hasLower = preg_match('/[a-z]/', $key);
        $hasUpper = preg_match('/[A-Z]/', $key);
        $hasNumber = preg_match('/[0-9]/', $key);
        $hasSymbol = preg_match('/[^a-zA-Z0-9]/', $key);

        $complexity = $hasLower + $hasUpper + $hasNumber + $hasSymbol;

        return $complexity >= 3;
    }

    /**
     * Secure random bytes generation.
     */
    public function generateSecureBytes(int $length = 32): string
    {
        return random_bytes($length);
    }

    /**
     * Convert bytes to hex string.
     */
    public function bytesToHex(string $bytes): string
    {
        return bin2hex($bytes);
    }

    /**
     * Convert hex string to bytes.
     */
    public function hexToBytes(string $hex): string
    {
        return hex2bin($hex);
    }
}
