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

    public function switch(Vault $vault)
    {
        $user = auth()->user();
        if (!$user->vaults()->where('coffre_id', $vault->id)->exists()) {
            return back()->with('error', 'Vous n\'avez pas accès à ce coffre.');
        }
        session(['active_vault_id' => $vault->id]);
    }
}
