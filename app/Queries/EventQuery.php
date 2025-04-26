<?php

namespace App\Queries;
use App\Models\Events\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Events\Registration as EventRegistration;

class EventQuery
{

    protected Builder $query;


    public function events(int $perPage = 10): LengthAwarePaginator
    {
        return Event::query()
            ->withCount('registrations')
            ->paginate($perPage);
    }

    public function schedule(array $data): Event
    {
        return Event::create($data);
    }

    public function event(string $eventId): Event
    {
        return Event::findOrFail($eventId);
    }


    public function hasAvailableSlots(string $eventId): bool
    {
        $event = Event::withCount('participants')
            ->lockForUpdate() 
            ->findOrFail($eventId);

        return $event->participants_count < $event->max_participants;
    }


    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }


    
    public function hasOverlappingRegistration(string $userId, string $startTime, string $endTime): bool
    {
        return Event::query()
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->exists();
    }



    public function getQuery(): Builder
    {
        return $this->query;
    }


    public function eventRegister(array $data): EventRegistration
    {
        return EventRegistration::create($data);
    }

}
