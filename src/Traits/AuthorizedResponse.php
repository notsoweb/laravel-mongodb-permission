<?php namespace Notsoweb\LaravelMongoDB\Permission\Traits;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use Illuminate\Http\Request;
use Notsoweb\ApiResponse\Enums\ApiResponse;
use Notsoweb\LaravelMongoDB\Permission\Exceptions\UnauthorizedException;

/**
 * Autorización de middleware
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
trait AuthorizedResponse
{
    /**
     * Solicitud no autorizada
     */
    protected function unauthorizedResponse(Request $request)
    {
        if ($request->expectsJson()) {
            return ApiResponse::FORBIDDEN->response([
                'error' => 'Unauthorized'
            ]);
        } else {
            throw UnauthorizedException::notLoggedIn();
        }
    }

    /**
     * Acción no autorizada
     */
    protected function unauthorizedActionResponse(Request $request)
    {
        if ($request->expectsJson()) {
            return ApiResponse::FORBIDDEN->response([
                'error' => 'Unauthorized'
            ]);
        } else {
            throw UnauthorizedException::actionNotAllow();
        }
    }
}