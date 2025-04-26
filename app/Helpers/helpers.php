<?php

use App\Services\Auth\RoleService;
use Illuminate\Auth\Access\AuthorizationException;

if (!function_exists('glimpseResponse')) {
    /**
     * Generate a JSON response.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param string|null $message
     * @param array $headers
     * @param bool $status
     * @return \Illuminate\Http\JsonResponse
     */
    function glimpseResponse($data, int $statusCode = 200, ?string $message = null, bool $status = true, array $headers = []): \Illuminate\Http\JsonResponse
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($response, $statusCode, $headers);
    }
}





if (!function_exists('authorizedRole')) {
    function authorizedRole(string|array $roles, $user = null): void
    {
        $user = $user ?? auth()->user();
        $roleService = app()->make(RoleService::class);

        foreach ((array) $roles as $role) {
            if ($roleService->userHasRole($user, $role)) {
                return;
            }
        }

        throw new AuthorizationException('Only ' . implode(', ', (array) $roles) . ' authorized action!');
    }
}
