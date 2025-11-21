<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'message' => $this->message,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'from_user' => new UserResource($this->whenLoaded('fromUser')),
            'notifiable_type' => $this->notifiable_type,
            'notifiable_id' => $this->notifiable_id,
        ];
    }
}

