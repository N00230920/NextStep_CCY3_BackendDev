<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;
use App\Http\Resources\CvResource;

class CvController extends BaseController
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        $cvs = auth()->user()->cvs()->get();
    
        return $this->sendResponse([
            'items' => CvResource::collection($cvs),
        ], 'CVs retrieved successfully');
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(StoreCvRequest $request): JsonResponse
    {
        $cv = $request->user()->cvs()->create($request->validated());

        return $this->sendResponse(new CvResource($cv), 'CV created successfully');
    }

    /**
     * Display the specified resource
     */
    public function show($id): JsonResponse
    {
        $cv = auth()->user()->cvs()->find($id);

        if (is_null($cv)) {
            return $this->sendError([], 'CV not found', 404);
        }

        return $this->sendResponse(
            new CvResource($cv),
            'CV retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage
     */
    public function update(UpdateCvRequest $request, $id): JsonResponse
    {
        $cv = auth()->user()->cvs()->find($id);

        if (is_null($cv)) {
            return $this->sendError([], 'CV not found', 404);
        }

        $input = $request->validated();

        $cv->name = $input['name'];
        $cv->email = $input['email'];
        $cv->phone = $input['phone'];
        $cv->location = $input['location'] ?? null;
        $cv->links = $input['links'] ?? null;
        $cv->bio = $input['bio'] ?? null;
        $cv->experience = $input['experience'] ?? null;
        $cv->education = $input['education'] ?? null;
        $cv->skills = $input['skills'] ?? null;
        $cv->save();

        return $this->sendResponse(new CvResource($cv), 'CV updated successfully');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($id): JsonResponse
    {
        $cv = auth()->user()->cvs()->find($id);

        if (is_null($cv)) {
            return $this->sendError([], 'CV not found', 404);
        }

        $cv->delete();

        return $this->sendResponse([], 'CV deleted successfully');
    }
}
