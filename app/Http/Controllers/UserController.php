<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\SharedService;

class UserController extends Controller
{
    public function __construct(private UserService $userService, private SharedService $sharedService) {}

    public function show()
    {
        return response(auth('sanctum')->user(), 201);
    }

    public function signup(SignUpRequest $request): JsonResponse
    {
        $response = $this->userService->signupUser($request->validated());

        return $this->sharedService->handleServiceResponse($response);
    }

    public function loginWithToken(Request $request): JsonResponse
    {
        $response = $this->userService->loginWithToken($request->bearerToken());

        return $this->sharedService->handleServiceResponse($response);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->userService->loginUser($request->validated());

        return $this->sharedService->handleServiceResponse($response);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->where('name', $request->user()->id)->delete();

        return response()->json(['message' => 'You have successfully logged out'], 204);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {

    }
}