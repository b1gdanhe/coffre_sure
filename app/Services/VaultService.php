<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vault;
use App\Models\Device;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Repositories\VaultRepository;

class VaultService
{

    private readonly EncryptionService $encryptionService;
    private readonly VaultRepository $vaultRepository;

    public function __construct(EncryptionService $encryptionService, VaultRepository $vaultRepository)
    {
        $this->encryptionService = $encryptionService;
        $this->vaultRepository = $vaultRepository;
    }

    public function switch(string $vaultId)
    {
        $user = Auth::user();
        if (!$user->vaults()->where('id', $vaultId)->exists()) {
            return back()->with('error', 'Vous n\'avez pas accès à ce coffre.');
        }
        $user->vaults()->where('is_active', true)->update([
            'is_active' => false
        ]);
        $user->vaults()->where('id', $vaultId)->update([
            'is_active' => true
        ]);
    }
    public function createVault(string $userId, array $data): Vault
    {
        return DB::transaction(function () use ($userId, $data) {
            // If this should be default, unset current default
            if ($data['is_default'] ?? false) {
                $this->unsetDefaultVault($userId);
            }

            // Generate encryption key for the vault
            $encryptionKey = $this->encryptionService->encryptWithCustomKey($encryptionKey = bin2hex(random_bytes(32)));

            $vaultData = [
                'user_id' => $userId,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'encryption_key' => $encryptionKey,
                'is_default' => $data['is_default'] ?? false,
            ];

            $vault = $this->vaultRepository->create($vaultData);

            Log::info('Vault created', [
                'vault_id' => $vault->id,
                'user_id' => $userId,
                'is_default' => $vault->is_default,
            ]);

            return $vault;
        });
    }

    public function deleteVault(Vault $vault): bool
    {
        return DB::transaction(function () use ($vault) {
            // If this was the default vault, set another one as default
            if ($vault->is_default) {
                $this->setNewDefaultVault($vault->user_id, $vault->id);
            }

            $deleted = $this->vaultRepository->delete($vault);

            Log::info('Vault deleted', [
                'vault_id' => $vault->id,
                'user_id' => $vault->user_id,
            ]);

            return $deleted;
        });
    }

    /**
     * Get all vaults for a user.
     */
    public function getUserVaults(string $userId, bool $withEntries = false): Collection
    {
        return $this->vaultRepository->getUserVaults($userId, $withEntries);
    }

    /**
     * Get vault with detailed information.
     */
    public function getVaultWithDetails(string $vaultId): Vault
    {
        return $this->vaultRepository->getWithDetails($vaultId);
    }

    /**
     * Set a vault as default for the user.
     */
    public function setAsDefault(Vault $vault): Vault
    {
        return DB::transaction(function () use ($vault) {
            // Unset current default
            $this->unsetDefaultVault($vault->user_id);

            // Set this vault as default
            $vault = $this->vaultRepository->update($vault, ['is_default' => true]);

            Log::info('Default vault changed', [
                'vault_id' => $vault->id,
                'user_id' => $vault->user_id,
            ]);

            return $vault;
        });
    }

    /**
     * Share a vault with another user.
     */
    public function shareVault(Vault $vault, string $userEmail, string $permissions): array
    {
        return DB::transaction(function () use ($vault, $userEmail, $permissions) {
            $targetUser = User::where('email', $userEmail)->firstOrFail();

            // Check if already shared
            $existingShare = $vault->sharedUsers()
                ->where('shared_with_user_id', $targetUser->id)
                ->first();

            if ($existingShare) {
                // Update permissions
                $existingShare->update(['permissions' => $permissions]);
                $sharedVault = $existingShare;
            } else {
                // Create new share
                $sharedVault = $vault->sharedUsers()->create([
                    'shared_with_user_id' => $targetUser->id,
                    'permissions' => $permissions,
                    'shared_by_user_id' => Auth::id(),
                ]);
            }

            Log::info('Vault shared', [
                'vault_id' => $vault->id,
                'shared_with' => $targetUser->email,
                'permissions' => $permissions,
                'shared_by' => Auth::id(),
            ]);

            return [
                'shared_vault' => $sharedVault,
                'target_user' => $targetUser->only(['id', 'name', 'email']),
            ];
        });
    }

    /**
     * Get vault statistics.
     */
    public function getVaultStatistics(Vault $vault): array
    {
        return $this->vaultRepository->getStatistics($vault);
    }

    /**
     * Get user's default vault.
     */
    public function getDefaultVault(string $userId): ?Vault
    {
        return $this->vaultRepository->getDefaultVault($userId);
    }

    /**
     * Unset current default vault for user.
     */
    private function unsetDefaultVault(string $userId): void
    {
        $this->vaultRepository->unsetDefaultVault($userId);
    }

    /**
     * Set a new default vault when current default is deleted.
     */
    private function setNewDefaultVault(string $userId, string $excludeVaultId): void
    {
        $nextVault = $this->vaultRepository->getFirstNonDefaultVault($userId, $excludeVaultId);

        if ($nextVault) {
            $this->vaultRepository->update($nextVault, ['is_default' => true]);
        }
    }
}
