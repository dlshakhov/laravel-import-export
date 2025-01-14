<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\AuthenticateRequest;
use App\Http\Requests\Api\User\RegisterUseRequest;
use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthenticateUserController extends Controller
{
    /**
     * @param AuthenticateRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function auth(AuthenticateRequest $request): JsonResponse
    {
        $request->authenticate();

        return response()->json([
            'user' => (new UserResource($request->user))->resolve(),
            'token' => $request->user->createToken($request->get('email'))->plainTextToken,
        ]);
    }

    /**
     * @param RegisterUseRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(RegisterUseRequest $request): JsonResponse
    {
        $request->registerUser();

        return response()->json([
            'user' => (new UserResource($request->user))->resolve(),
            'token' => $request->user->createToken($request->get('email'))->plainTextToken,
        ]);
    }
}
