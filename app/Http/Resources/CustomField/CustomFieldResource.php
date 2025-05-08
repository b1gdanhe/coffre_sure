<?php

namespace App\Http\Resources\CustomField;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldResource extends JsonResource
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
            'entry_id' => $this->entry_id,
            'field_name' => $this->field_name,
            'field_value' => $this->when($this->shouldShowSensitiveData($request), $this->field_value),
            'field_type' => $this->field_type,
            'is_encrypted' => (bool) $this->is_encrypted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
