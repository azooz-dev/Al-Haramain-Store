<?php


namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

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
    if ($collection->isEmpty()) {
      return successResponse($collection, $message, $status);
    }

    $transformer = get_class($collection->first());

    $collection = paginateData(sortData(filterData($collection, $transformer), $transformer));

    return successResponse($collection, $message, $status);
  }
}

if (!function_exists('showOne')) {
  function showOne($model, $message = 'Success', $status = 200): JsonResponse
  {
    return successResponse($model, $message, $status);
  }
}


if (!function_exists('showMessage')) {
  function showMessage($message, $status = 200): JsonResponse
  {
    return response()->json([
      'message' => $message,
      'status' => 'success'
    ], $status);
  }
}

if (!function_exists('filterData')) {
  function filterData($collection, $transformer)
  {
    foreach (request()->query() as $query => $value) {
      $attribute = $transformer::transformAttribute($query);

      if (isset($attribute)) {
        $collection = $collection->where($attribute, $value);
      }
    }

    return $collection;
  }

  if (!function_exists('sortData')) {
    function sortData($collection, $transformer)
    {
      if (request()->has('sort_by')) {
        $attribute = $transformer::transformAttribute(request()->sort_by);

        $collection = $collection->sortBy($attribute);
      }

      return $collection;
    }
  }

  if (!function_exists('paginateData')) {
    function paginateData($collection)
    {
      request()->validate([
        'per_page' => 'integer|min:2|max:50',
      ]);

      $page = LengthAwarePaginator::resolveCurrentPage();

      $perPage = 15;

      if (request()->has('per_page')) {
        $perPage = (int) request()->per_page;
      }
      $result = $collection->slice(($page - 1) * $perPage, $perPage);
      $paginated = new LengthAwarePaginator($result, $collection->count(), $perPage, $page, [
        'path' => LengthAwarePaginator::resolveCurrentPath(),
      ]);

      $paginated->appends(request()->all());

      return $paginated;
    }

    if (!function_exists('cacheResponse')) {
      function cacheResponse($data)
      {
        // Get the current URL
        $url = request()->url();

        // Get the query parameters and sort them
        $queryParams = request()->query();
        ksort($queryParams);

        // Build the query string
        $queryString = http_build_query($queryParams);

        // Create the full URL with query string
        $fullUrl = $url . '?' . $queryString;

        // Set the cache time
        $time = 30; // 30 seconds

        // Return the cached data or generate it if it doesn't exist
        return Cache::remember($fullUrl, $time, function () use ($data) {
          return $data;
        });
      }
    }
  }
}
