<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Event\EventController;


Route::middleware(['auth:api'])->group(function () {
    Route::get('/', [EventController::class, 'events']);
    Route::post('/schedule', [EventController::class, 'schedule']);
    Route::get('/{eventId}', [EventController::class, 'event']);
    Route::post('/participant/register', [EventController::class, 'registerUser']);
});
