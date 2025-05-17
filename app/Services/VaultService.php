<?php

namespace App\Services;

use App\Models\User;
use App\Models\Device;
use App\Models\AccessLog;
use App\Models\Vault;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaultService
{

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
}
