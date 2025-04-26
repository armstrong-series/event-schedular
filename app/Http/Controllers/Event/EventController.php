<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Services\Events\EventService;
use App\Services\Events\EventRegistrationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\Event\EventRegisterRequest;
use App\Http\Requests\Event\EventRequest;
use App\Http\Resources\Event\EventResource;
use App\Http\Resources\Event\RegisterEventResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventController extends Controller
{

    public function __construct(
        protected EventService $eventService,
        protected EventRegistrationService $eventRegistrationService
    ) {}


    public function events(): JsonResponse
    {
        $events = $this->eventService->events();

        if ($events->isEmpty()) {
            return glimpseResponse(null, 404, "No record found");
        }

        $responseData = [
            'events' =>  EventResource::collection($events),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page'    => $events->lastPage(),
                'total'        => $events->total(),
            ]
        ];
        return glimpseResponse($responseData, 200, "Request completed!", true);
    }


    public function schedule(EventRequest $request): JsonResponse
    {
        authorizedRole(['admin', 'manager']);
        
        $event = $this->eventService->schedule($request->validated());
        return  glimpseResponse(new EventResource($event), 201, "Resource created!", true);
    }

    public function event(string $eventId): JsonResponse
    {
        try {
            $event = $this->eventService->event($eventId);
            return glimpseResponse(new EventResource($event), 200, "Request completed!", true);
        } catch (ModelNotFoundException $e) {
            return glimpseResponse([], 404, "No event record found");
        }
    }


    public function registerUser(EventRegisterRequest $request): JsonResponse
    {
    
        $registration = $this->eventRegistrationService->registerUser(
            $request->event_id,
            auth()->id()
        );
        return  glimpseResponse(new RegisterEventResource($registration), 201, "Resource created!", true);
    }
}
