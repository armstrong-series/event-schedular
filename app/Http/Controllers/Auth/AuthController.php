<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Auth\SignInRequest;

class AuthController extends Controller
{
    
    public function __construct(
        protected AuthService $authService
    ){}



    public function signin(SignInRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->signin($request->validated());
            return glimpseResponse($response['data'], 200,$response['message'], true);

        } catch (AuthenticationException $e) {
            return glimpseResponse([], 401, $e->getMessage(),false);
        }
    }

}
