<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Return a success JSON response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message = 'Error', $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error response.
     *
     * @param  mixed  $errors
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationErrorResponse($errors, $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Add cache control headers to prevent caching of API responses.
     *
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function withoutCache(JsonResponse $response): JsonResponse
    {
        return $response
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Add CORS headers for API responses.
     *
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function withCorsHeaders(JsonResponse $response): JsonResponse
    {
        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
}
