<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'start_time' => $this->start_time->toIso8601String(),
            'end_time'   => $this->end_time->toIso8601String(),
            'max_participants'     => $this->max_participants,
            'current_participants' => $this->registrations->count(),
        ];
    }
}
