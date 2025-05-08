<?php

namespace App\Http\Resources\Vault;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Entry\EntryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SharedVault\SharedVaultResource;

class VaultResource extends JsonResource
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
            'description' => $this->description,
            'icon' => $this->icon,
            'is_default' => (bool) $this->is_default,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'entries_count' => $this->whenCounted('entries'),
            'shared_users_count' => $this->whenCounted('sharedUsers'),
            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'user' => new UserResource($this->whenLoaded('user')),
            'shared_users' => UserResource::collection($this->whenLoaded('sharedUsers')),
            'shared_details' => $this->when($request->user() && $request->user()->id !== $this->user_id, 
                fn() => new SharedVaultResource($this->sharedVaults()->where('user_id', $request->user()->id)->first())
            ),
        ];
    }
}
