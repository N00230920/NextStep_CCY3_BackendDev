<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\StoreCoverRequest;
use App\Http\Requests\UpdateCoverRequest;
use App\Http\Resources\CoverResource;
use App\Models\Cover;

class CoverController extends BaseController
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        $covers = auth()->user()->covers()->latest()->paginate(request()->get('per_page', 10));
    
        return $this->sendResponse([
            'items' => CoverResource::collection($covers->items()),
            'pagination' => [
                'current_page' => $covers->currentPage(),
                'last_page' => $covers->lastPage(),
                'per_page' => $covers->perPage(),
                'total' => $covers->total(),
            ]
        ], 'Cover letters retrieved successfully');
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(StoreCoverRequest $request): JsonResponse
    {
        $input = $request->validated();
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
    public function update(UpdateCoverRequest $request, $id): JsonResponse
    {
        $cover = auth()->user()->covers()->find($id);

        if (is_null($cover)) {
            return $this->sendError('Cover letter not found');
        }

        $input = $request->validated();

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
            return $this->sendError('Cover letter not found');
        }

        $cover->delete();

        return $this->sendResponse([], 'Cover letter deleted successfully');
    }
}