<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Events\Event;

class EventRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_id' => [
                'required',
                'uuid',
                'exists:events,id',
                function ($attribute, $value, $fail) {
                    $event = Event::withCount('participants')->find($value);
                    if ($event && $event->participants_count >= $event->max_participants) {
                        $fail('This event has reached its maximum capacity.');
                    }
                },
                function ($attribute, $value, $fail) {
                    $event = Event::find($value);
                    if ($event && $this->hasOverlappingEvents($event)) {
                        $fail('You are already registered for an overlapping event.');
                    }
                },
            ],
        ];
    }


    protected function hasOverlappingEvents(Event $event): bool
    {
        return auth()->user()->events()
            ->where(function ($query) use ($event) {
                $query->whereBetween('start_time', [$event->start_time, $event->end_time])
                      ->orWhereBetween('end_time', [$event->start_time, $event->end_time])
                      ->orWhere(function ($q) use ($event) {
                          $q->where('start_time', '<=', $event->start_time)
                            ->where('end_time', '>=', $event->end_time);
                      });
            })
            ->exists();
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors()
        ], 422));
    }
}
