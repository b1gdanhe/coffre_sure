<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Vault\VaultResource;
use App\Http\Resources\Device\DeviceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'last_login' => $this->last_login,
            'mfa_enabled' => (bool) $this->mfa_enabled,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'vaults_count' => $this->whenCounted('vaults'),
            'devices_count' => $this->whenCounted('devices'),
            'vaults' => VaultResource::collection($this->whenLoaded('vaults')),
            'devices' => DeviceResource::collection($this->whenLoaded('devices')),


        ];
    }
}
