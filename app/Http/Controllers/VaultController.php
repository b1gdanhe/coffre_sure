<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Vault;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\Vault\VaultResource;
use App\Http\Resources\Vault\VaultCollection;

class VaultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('vaults/Index', [
            'vaults' => VaultResource::collection(Vault::where(
                'user_id',
                \request()->user()->id
            )->get())
        ]);
    }

    public function createDefaultVault(User $user, $encryptionKey)
    {
        // Générer une clé de chiffrement pour le coffre-fort
        $vaultKey = bin2hex(random_bytes(32));

        // Chiffrer la clé du coffre-fort avec la clé de l'utilisateur
        $encryptedVaultKey = openssl_encrypt(
            $vaultKey,
            'aes-256-gcm',
            $encryptionKey,
            0,
            random_bytes(16),
            $tag
        );

        // Créer le coffre-fort par défaut
        $vault = Vault::create([
            'user_id' => $user->id,
            'name' => 'Mon coffre',
            'description' => 'Votre coffre-fort par défaut',
            'encryption_key' => $encryptedVaultKey,
            'is_default' => true
        ]);

        return $vault;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
