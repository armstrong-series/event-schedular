<?php

namespace App\Services\Events;
use App\Queries\EventQuery;
use App\Contracts\EventInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class EventService implements EventInterface
{

    public function __construct(
        protected EventQuery $eventQuery
    ){}


    public function events(): LengthAwarePaginator
    {
        return $this->eventQuery->events();
    }

    public function schedule(array $data): Model
    {
        return $this->eventQuery->schedule($data);
    }

    public function event(string $eventId): mixed
    {
        return $this->eventQuery->event($eventId);
    }

    public function hasAvailableSlots(string $eventId): bool
    {
        return $this->eventQuery->hasAvailableSlots($eventId);
    }

    public function hasOverlappingRegistration(string $userId, string $startTime, string $endTime): bool
    {
        return $this->eventQuery->hasOverlappingRegistration($userId, $startTime, $endTime);
    }

}
