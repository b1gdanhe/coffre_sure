<?php

namespace App\Http\Resources\AccessLog;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessLogResource extends JsonResource
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
            'user_id' => $this->user_id,
            'action' => $this->action,
            'details' => $this->details,
            'ip_address' => $this->ip_address,
            'device_info' => $this->device_info,
            'status' => $this->status,
            'created_at' => Carbon:: createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d/m/Y H:i'),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
