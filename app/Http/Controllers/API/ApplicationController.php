<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Validator;

class ApplicationController extends BaseController
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        $applications = auth()->user()->applications;

        return $this->sendResponse(
            ApplicationResource::collection($applications),
            'Applications retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'cv_id' => 'nullable|exists:cvs,id',
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'salary' => 'nullable|integer',
            'status' => 'required|in:applied,interview,offer,rejected,ghosted',
            'job_type' => 'nullable|in:full-time,part-time,internship,contract',
            'job_url' => 'nullable|string',
            'notes' => 'nullable|string',
            'applied_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

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
            return $this->sendError('Application not found');
        }

        return $this->sendResponse(
            new ApplicationResource($application),
            'Application retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, $id): JsonResponse
    {
        $application = auth()->user()->applications()->find($id);

        if (is_null($application)) {
            return $this->sendError('Application not found');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'cv_id' => 'nullable|exists:cvs,id',
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'salary' => 'nullable|integer',
            'status' => 'required|in:applied,interview,offer,rejected,ghosted',
            'job_type' => 'nullable|in:full-time,part-time,internship,contract',
            'job_url' => 'nullable|string',
            'notes' => 'nullable|string',
            'applied_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

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
            return $this->sendError('Application not found');
        }

        $application->delete();

        return $this->sendResponse([], 'Application deleted successfully');
    }
}