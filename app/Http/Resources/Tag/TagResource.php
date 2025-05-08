<?php

namespace App\Http\Resources\Tag;

use Illuminate\Http\Request;
use App\Http\Resources\Entry\EntryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'color' => $this->color,
            'created_at' => $this->created_at,
            'entries_count' => $this->whenCounted('entries'),
            'entries' => EntryResource::collection($this->whenLoaded('entries')),
        ];
    }
}
