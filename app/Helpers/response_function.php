<?php


namespace App\Helpers;

use Illuminate\Http\JsonResponse;

if (!function_exists('successResponse')) {
  function successResponse($data, $message = 'Success', $status = 200): JsonResponse
  {
    return response()->json([
      'data' => $data,
      'message' => $message,
      'status' => 'success'
    ], $status);
  }
}


if (!function_exists('errorResponse')) {
  function errorResponse($message = 'Error', $status = 400): JsonResponse
  {
    return response()->json([
      'message' => $message,
      'status' => 'error'
    ], $status);
  }
}

if (!function_exists('showAll')) {
  function showAll($collection, $message = 'Success', $status = 200): JsonResponse
  {
    return successResponse($collection, $message, $status);
  }
}

if (!function_exists('showOne')) {
  function showOne($model, $message = 'Success', $status = 200): JsonResponse
  {
    return successResponse($model, $message, $status);
  }
}
