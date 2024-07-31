<?php namespace Notsoweb\LaravelMongoDB\Permission\Exceptions;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Excepciones de autorización
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
class UnauthorizedException extends HttpException
{
    /**
     * Usuario no autenticado
     */
    public static function notLoggedIn(): self
    {
        return new static(403, __('Usuario no autenticado'), null, []);
    }

    /**
     * Acción no permitida
     */
    public static function actionNotAllow(): self
    {
        return new static(403, __('Acción no permitida'), null, []);
    }
}