<?php

namespace App\Http\Resources\Entry;

use Illuminate\Http\Request;
use App\Http\Resources\Tag\TagResource;
use App\Http\Resources\Vault\VaultResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomField\CustomFieldResource;

class EntryResource extends JsonResource
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
            'vault_id' => $this->vault_id,
            'title' => $this->title,
            'username' => $this->username,
           // 'password' => $this->when($this->shouldShowSensitiveData($request), $this->password),
            'url' => $this->url,
          //  'notes' => $this->when($this->shouldShowSensitiveData($request), $this->notes),
            'icon' => $this->icon,
            'last_used' => $this->last_used,
            'category' => $this->category,
            'favorite' => (bool) $this->favorite,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'custom_fields' => CustomFieldResource::collection($this->whenLoaded('customFields')),
            'vault' => new VaultResource($this->whenLoaded('vault')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
