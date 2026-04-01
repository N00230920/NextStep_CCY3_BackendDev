<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;

class EventController extends BaseController
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        $events = auth()->user()->events()
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->paginate(request()->get('per_page', 10));
    
        return $this->sendResponse([
            'items' => EventResource::collection($events->items()),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ], 'Events retrieved successfully');
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $input = $request->validated();
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
            return $this->sendError([], 'Event not found.');
        }

        return $this->sendResponse(
            new EventResource($event),
            'Event retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage
     */
    public function update(UpdateEventRequest $request, $id): JsonResponse
    {
        $event = auth()->user()->events()->find($id);

        if (is_null($event)) {
            return $this->sendError([], 'Event not found.');
        }

        $input = $request->validated();

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
            return $this->sendError([], 'Event not found.');
        }

        $event->delete();

        return $this->sendResponse([], 'Event deleted successfully');
    }

    public function upcoming(): JsonResponse
    {
        $events = auth()->user()->events()
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->take(10)
            ->get();

        return $this->sendResponse(
            EventResource::collection($events),
            'Upcoming events retrieved successfully'
        );
    }
}
