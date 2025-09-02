<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackLastActivity
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (Auth::guard('admin')->check()) {
      $adminId = Auth::guard('admin')->id();
      session(['last_activity_' . $adminId => now()]);
    }

    return $next($request);
  }
}
