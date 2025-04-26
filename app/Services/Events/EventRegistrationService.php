<?php

namespace App\Services\Events;

use App\Queries\EventQuery;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class EventRegistrationService
{

    public function __construct(
        public EventQuery $eventQuery
    ) {}



    public function registerUser(string $eventId, string $userId)
    {
        return DB::transaction(function () use ($eventId, $userId) {
            $event = $this->eventQuery->event($eventId);

            if (!$this->eventQuery->hasAvailableSlots($eventId)) {
                throw new InvalidArgumentException('Max event participant limit reached!');
            }

            if ($this->eventQuery->hasOverlappingRegistration(
                $userId,
                $event->start_time,
                $event->end_time
            )) {
                throw new InvalidArgumentException('User has overlapping event');
            }

            return $this->eventQuery->eventRegister(
                [
                    'event_id' => $eventId,
                    'user_id'  => $userId
                ]
            );
        });
    }
}
