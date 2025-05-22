<?php

namespace App\Repositories;

use App\Models\Vault;
use Illuminate\Database\Eloquent\Collection;

class VaultRepository
{
    /**
     * Create a new vault.
     */
    public function create(array $data): Vault
    {
        return Vault::create($data);
    }

    /**
     * Update an existing vault.
     */
    public function update(Vault $vault, array $data): Vault
    {
        $vault->update($data);
        return $vault->fresh();
    }

    /**
     * Delete a vault.
     */
    public function delete(Vault $vault): bool
    {
        return $vault->delete();
    }

    /**
     * Find vault by ID with error handling.
     */
    public function findOrFail(string $id): Vault
    {
        return Vault::findOrFail($id);
    }

    /**
     * Get all vaults for a specific user.
     */
    public function getUserVaults(string $userId, bool $withEntries = false): Collection
    {
        $query = Vault::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc');

        if ($withEntries) {
            $query->with(['entries' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }]);
        }

        return $query->get();
    }

    /**
     * Get vault with detailed information.
     */
    public function getWithDetails(string $vaultId): Vault
    {
        return Vault::with([
            'entries.tags',
            'sharedUsers.sharedWithUser:id,name,email',
            'user:id,name,email'
        ])->findOrFail($vaultId);
    }

    /**
     * Get user's default vault.
     */
    public function getDefaultVault(string $userId): ?Vault
    {
        return Vault::where('user_id', $userId)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Unset all default vaults for a user.
     */
    public function unsetDefaultVault(string $userId): int
    {
        return Vault::where('user_id', $userId)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    /**
     * Get first non-default vault for user (excluding specific vault).
     */
    public function getFirstNonDefaultVault(string $userId, string $excludeVaultId): ?Vault
    {
        return Vault::where('user_id', $userId)
            ->where('id', '!=', $excludeVaultId)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Get vault statistics.
     */
    public function getStatistics(Vault $vault): array
    {
        $totalEntries = $vault->entries()->count();
        $entriesByCategory = $vault->entries()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $favoriteEntries = $vault->entries()
            ->where('favorite', true)
            ->count();

        $recentlyAdded = $vault->entries()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $sharedWith = $vault->sharedUsers()->count();

        return [
            'total_entries' => $totalEntries,
            'entries_by_category' => $entriesByCategory,
            'favorite_entries' => $favoriteEntries,
            'recently_added' => $recentlyAdded,
            'shared_with_users' => $sharedWith,
            'created_at' => $vault->created_at,
            'updated_at' => $vault->updated_at,
        ];
    }

    /**
     * Search vaults by name for a user.
     */
    public function searchByName(string $userId, string $searchTerm): Collection
    {
        return Vault::where('user_id', $userId)
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get shared vaults for a user.
     */
    public function getSharedVaults(string $userId): Collection
    {
        return Vault::whereHas('sharedUsers', function ($query) use ($userId) {
            $query->where('shared_with_user_id', $userId);
        })->with(['user:id,name,email', 'sharedUsers' => function ($query) use ($userId) {
            $query->where('shared_with_user_id', $userId);
        }])->get();
    }

    /**
     * Check if user has access to vault.
     */
    public function userHasAccess(string $userId, string $vaultId): bool
    {
        return Vault::where('id', $vaultId)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereHas('sharedUsers', function ($subQuery) use ($userId) {
                        $subQuery->where('shared_with_user_id', $userId);
                    });
            })->exists();
    }
}