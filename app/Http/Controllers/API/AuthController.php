<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;

use Validator;

class AuthController extends BaseController
{
    /**
     * Register a new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        $input = $request->only(['name', 'email', 'password']);
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('NextStep')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User registered successfully.');
    }

    public function login(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }
        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('NextStep')->plainTextToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'User logged in successfully.');
        }

        else {
            return $this->sendError('Unauthorized', ['error' => 'Invalid credentials.'], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'Logged out successfully');
    }

    public function user(Request $request): JsonResponse
    {
        return $this->sendResponse($request->user(), 'User retrieved successfully');
    }
}
