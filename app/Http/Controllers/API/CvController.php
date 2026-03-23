<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CvResource;
use App\Models\Cv;
use Validator;

class CvController extends BaseController
{

    /**
     * Display a listing of the resource
     * 
     * @return \Illuminate\Http\Response
     */
    public function index():JsonResponse
    {
        $cvs = auth()->user()->cvs;

        return $this->sendResponse(
            CvResource::collection($cvs),
            'CVs retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage
     * 
     * @param \Illuminate\Http\Request $equest
     * @return \Illuminate\Http\Response 
     */
    public function store(Request $request):JsonResponse 
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits_between:10,15',
            'location' => 'nullable|string',
            'links' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error',$validator->errors());
        }

        $input['user_id'] = auth()->id();

        $cv = Cv::create($input);

        return $this->sendResponse(new CvResource($cv), 'Cv created successfully');
    }

    /**
 * Display the specified resource
 * 
 * @param int $id
 * @return \Illuminate\Http\Response
 */
    public function show($id):JsonResponse
    {
        $cv = auth()->user()->cvs()->find($id);

        if (is_null($cv)) {
            return $this->sendError('CV not found');
        }

        return $this->sendResponse(
            new CvResource($cv),
            'CV retrieved successfully'
        );
    }

    /**
 * Updatethe specified resource in storage
 * 
 * @param \Illuminate\Http\Request $request
 * @param int $id
 * @return \Illuminate\Http\Response
 */
    public function update(Request $request, $id): JsonResponse
    {
        $cv = auth()->user()->cvs()->find($id);

            if (is_null($cv)) {
                return $this->sendError('CV not found');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits_between:10,15',
            'location' => 'nullable|string',
            'links' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

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

        return $this->sendResponse(new CvResource($cv), 'Cv updated successfully');
    }

    /**
 * Removes the specified resource in storage
 * 
 * @param int $id
 * @return \Illuminate\Http\Response
 */
    public function destroy($id):JsonResponse
    {
        $cv = auth()->user()->cvs()->find($id);

        if (is_null($cv)) {
            return $this->sendError('CV not found');
        }

        $cv->delete();

        return $this->sendResponse([], 'CV deleted successfully');
    }
}
