<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Vault;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\VaultService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreVaultRequest;
use App\Http\Resources\Vault\VaultResource;
use App\Http\Resources\Vault\VaultCollection;

class VaultController extends Controller
{

    private VaultService $vaultService;


    public function __construct(VaultService $vaultService)
    {
        $this->vaultService = $vaultService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('admin/vaults/Index', [
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

    public function switchVault(Request $request)
    {
        $request->validate([
            'selectedVaultId' => 'required|exists:vaults,id'
        ]);
        $this->vaultService->switch($request->selectedVaultId);
        return back()->with('success', 'Coffre changé avec succès.');
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
    public function store(StoreVaultRequest $request)
    {
        try {
            $vault = $this->vaultService->createVault(
                userId: Auth::user()->id,
                data: $request->validated()
            );

            return redirect()->route('entries.index')->with('success', 'Entry created successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de la création du coffre: ' . $e->getMessage()]);
        }
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
