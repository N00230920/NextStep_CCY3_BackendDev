<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Http\Resources\EventResource;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;


use Validator;


class ApplicationController extends BaseController
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        $query = auth()->user()->applications();
    
        if (request()->has('status') && request('status') !== '') {
            $query->where('status', request('status'));
        }
    
        if (request()->has('job_type') && request('job_type') !== '') {
            $query->where('job_type', request('job_type'));
        }
    
        if (request()->has('company_name') && request('company_name') !== '') {
            $query->where('company_name', 'like', '%' . request('company_name') . '%');
        }
    
        $applications = $query->latest()->paginate(request()->get('per_page', 10));
    
        return $this->sendResponse([
            'items' => ApplicationResource::collection($applications->items()),
            'pagination' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
            ]
        ], 'Applications retrieved successfully');
    }
    /**
     * Store a newly created resource in storage
     */
    public function store(StoreApplicationRequest $request): JsonResponse
    {
        $input = $request->validated();
        $input['user_id'] = auth()->id();
    
        $application = Application::create($input);
    
        return $this->sendResponse(
            new ApplicationResource($application),
            'Application created successfully'
        );
    }

    /**
     * Display the specified resource
     */
    public function show($id): JsonResponse
    {
        $application = auth()->user()->applications()->find($id);

        if (is_null($application)) {
            return $this->sendError([], 'Application not found');
        }

        return $this->sendResponse(
            new ApplicationResource($application),
            'Application retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage
     */
    public function update(UpdateApplicationRequest $request, $id): JsonResponse
    {
        $application = auth()->user()->applications()->find($id);
    
        if (is_null($application)) {
            return $this->sendError([], 'Application not found');
        }
    
        $input = $request->validated();
    
        $application->cv_id = $input['cv_id'] ?? null;
        $application->company_name = $input['company_name'];
        $application->position = $input['position'];
        $application->location = $input['location'] ?? null;
        $application->contact_email = $input['contact_email'] ?? null;
        $application->salary = $input['salary'] ?? null;
        $application->status = $input['status'];
        $application->job_type = $input['job_type'] ?? null;
        $application->job_url = $input['job_url'] ?? null;
        $application->notes = $input['notes'] ?? null;
        $application->applied_date = $input['applied_date'] ?? null;
        $application->save();
    
        return $this->sendResponse(
            new ApplicationResource($application),
            'Application updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($id): JsonResponse
    {
        $application = auth()->user()->applications()->find($id);

        if (is_null($application)) {
            return $this->sendError([], 'Application not found');
        }

        $application->delete();

        return $this->sendResponse([], 'Application deleted successfully');
    }

    public function dashboard(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return $this->sendError([], 'Unauthenticated.', 401);
        }

        $statusCounts = [
            'applied' => $user->applications()->where('status', 'applied')->count(),
            'interview' => $user->applications()->where('status', 'interview')->count(),
            'offer' => $user->applications()->where('status', 'offer')->count(),
            'rejected' => $user->applications()->where('status', 'rejected')->count(),
            'ghosted' => $user->applications()->where('status', 'ghosted')->count(),
        ];

        $recentApplications = $user->applications()
            ->latest()
            ->take(5)
            ->get();

        $upcomingEvents = $user->events()
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->take(5)
            ->get();

        $data = [
            'status_counts' => $statusCounts,
            'recent_applications' => ApplicationResource::collection($recentApplications),
            'upcoming_events' => EventResource::collection($upcomingEvents),
        ];

        return $this->sendResponse($data, 'Dashboard data retrieved successfully');
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:applied,interview,offer,rejected,ghosted',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        $application = auth()->user()->applications()->find($id);

        if (is_null($application)) {
            return $this->sendError([], 'Application not found');
        }

        $application->status = $request->status;
        $application->save();

        return $this->sendResponse(
            new ApplicationResource($application),
            'Application status updated successfully'
        );
    }
}
