<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSessionPath
{
    /**
     * Handle an incoming request.
     *
     * This middleware sets admin-specific session cookie NAME (not path).
     * It ONLY runs for admin panel routes (registered in AdminPanelProvider).
     * This completely isolates admin sessions from frontend sessions.
     * 
     * IMPORTANT: We use the SAME path (/) for both admin and frontend cookies
     * to ensure they're accessible across the entire domain. The different
     * cookie NAMES provide the isolation we need.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Determine if this is an admin-related request
        // Since frontend is React (no Livewire), assume ALL Livewire requests belong to Admin
        $uri = $request->getRequestUri();
        $isAdmin = str_contains($uri, '/admin') || str_contains($uri, '/livewire');
        
        if ($isAdmin) {
            // Admin Panel & Livewire: Use 'laravel-admin' cookie
            config([
                'session.cookie' => env('SESSION_COOKIE', 'laravel') . '-admin',
                'session.path' => '/', 
            ]);
            
            \Log::info('SetSessionPath [ADMIN]: Configured admin session', [
                'uri' => $uri,
                'cookie' => config('session.cookie')
            ]);
        } else {
            // Frontend/API: Explicitly use default 'laravel-session' to ensure isolation
            // Only strictly needed if we want to force separation on this side too
             config([
                'session.cookie' => env('SESSION_COOKIE', 'laravel') . '-session',
                'session.path' => '/', 
            ]);
            
             \Log::info('SetSessionPath [FRONTEND]: Configured frontend session', [
                'uri' => $uri,
                'cookie' => config('session.cookie')
            ]);
        }
        
        return $next($request);
    }
}
