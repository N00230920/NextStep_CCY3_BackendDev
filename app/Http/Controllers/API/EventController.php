<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Validator;

class EventController extends BaseController
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        $events = auth()->user()->events;

        return $this->sendResponse(
            EventResource::collection($events),
            'Events retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'application_id' => 'nullable|exists:applications,id',
            'title' => 'required|string|max:255',
            'event_type' => 'required|in:interview,reminder,assessment,call,deadline',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_all_day' => 'required|boolean',
            'event_time' => 'nullable|date_format:H:i|required_if:is_all_day,false',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input['user_id'] = auth()->id();

        if ($input['is_all_day']) {
            $input['event_time'] = null;
        }

        $event = Event::create($input);

        return $this->sendResponse(
            new EventResource($event),
            'Event created successfully'
        );
    }

    /**
     * Display the specified resource
     */
    public function show($id): JsonResponse
    {
        $event = auth()->user()->events()->find($id);

        if (is_null($event)) {
            return $this->sendError('Event not found');
        }

        return $this->sendResponse(
            new EventResource($event),
            'Event retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, $id): JsonResponse
    {
        $event = auth()->user()->events()->find($id);

            if (is_null($event)) {
                return $this->sendError('Event not found');
        }
        $input = $request->all();

        $validator = Validator::make($input, [
            'application_id' => 'nullable|exists:applications,id',
            'title' => 'required|string|max:255',
            'event_type' => 'required|in:interview,reminder,assessment,call,deadline',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_all_day' => 'required|boolean',
            'event_time' => 'nullable|date_format:H:i|required_if:is_all_day,false',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $event->application_id = $input['application_id'] ?? null;
        $event->title = $input['title'];
        $event->event_type = $input['event_type'];
        $event->description = $input['description'] ?? null;
        $event->event_date = $input['event_date'];
        $event->is_all_day = $input['is_all_day'];

        if ($input['is_all_day']) {
            $event->event_time = null;
        } else {
            $event->event_time = $input['event_time'];
        }

        $event->location = $input['location'] ?? null;

        $event->save();

        return $this->sendResponse(
            new EventResource($event),
            'Event updated successfully'
        );
    }

    /**
     * Remove the specified resource
     */
    public function destroy($id): JsonResponse
    {
        $event = auth()->user()->events()->find($id);

        if (is_null($event)) {
            return $this->sendError('Event not found');
        }

        $event->delete();

        return $this->sendResponse([], 'Event deleted successfully');
    }
}
