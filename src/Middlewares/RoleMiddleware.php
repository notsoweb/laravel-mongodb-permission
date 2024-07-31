<?php namespace Notsoweb\LaravelMongoDB\Permission\Middlewares;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Notsoweb\LaravelMongoDB\Permission\Traits\AuthorizedResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para detectar role usuario
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
class RoleMiddleware
{
    use AuthorizedResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role, $guard = null): Response
    {
        $authGuard = Auth::guard($guard);

        $user = $authGuard->user();

        if (!$user) {
            return $this->unauthorizedResponse($request);
        }

        if (!$user->hasRole($role)) {
            return $this->unauthorizedActionResponse($request);
        }

        return $next($request);
    }
}
