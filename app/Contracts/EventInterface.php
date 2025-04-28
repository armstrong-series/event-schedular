<?php

namespace App\Contracts;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EventInterface
{

    public function events(): LengthAwarePaginator;

    public function schedule(array $data): mixed;
    
    public function event(string $id): mixed;

    public function hasAvailableSlots(string $eventId): bool;

    public function hasOverlappingRegistration(string $userId, string $startTime, string $endTime): bool;

}
