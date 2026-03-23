<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CoverResource;
use App\Models\Cover;
use Validator;

class CoverController extends BaseController
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        $covers = auth()->user()->covers;

        return $this->sendResponse(
            CoverResource::collection($covers),
            'Cover letters retrieved successfully'
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
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input['user_id'] = auth()->id();

        $cover = Cover::create($input);

        return $this->sendResponse(
            new CoverResource($cover),
            'Cover letter created successfully'
        );
    }

    /**
     * Display the specified resource
     */
    public function show($id): JsonResponse
    {
        $cover = auth()->user()->covers()->find($id);

        if (is_null($cover)) {
            return $this->sendError('Cover letter not found');
        }

        return $this->sendResponse(
            new CoverResource($cover),
            'Cover letter retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, $id): JsonResponse
    {
        $cover = auth()->user()->covers()->find($id);

            if (is_null($cover)) {
                return $this->sendError('Cover Letter not found');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'application_id' => 'nullable|exists:applications,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $cover->application_id = $input['application_id'] ?? null;
        $cover->title = $input['title'];
        $cover->content = $input['content'];
        $cover->save();

        return $this->sendResponse(
            new CoverResource($cover),
            'Cover letter updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($id): JsonResponse
    {
        $cover = auth()->user()->covers()->find($id);

        if (is_null($cover)) {
            return $this->sendError('Cover Letter not found');
        }

        $cover->delete();

        return $this->sendResponse([], 'Cover Letter deleted successfully');
    }
}