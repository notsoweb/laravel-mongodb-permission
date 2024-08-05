<?php namespace Notsoweb\LaravelMongoDB\Permission\Traits;
/**
 * @copyright 2023 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use stdClass;

 /**
 * Crea usuarios seguros
 * 
 * Permite crear usuarios en el seeder para que no queden guardadas contraseñas en el
 * git o en alguna parte del código.
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
trait UserSecurePassword
{
    /**
     * Canal donde se guarda el registro
     */
    const LOG_CHANNEL = 'notsoweb:users';

    /**
     * Genera una contraseña random para un usuario determinado y lo registra en el log.
     * 
     * Permite generar una contraseña para cualquier usuario, esta queda guardada en el log en
     * storage/logs/notsoweb/users.log
     */
    public static function securePassword(string $email, int $length = 12) : stdClass
    {
        $password = Str::random($length);
 
        Log::channel(self::LOG_CHANNEL)->info("SecurePassword: {$email} => {$password}");

        $hash = bcrypt($password);

        echo "\n  Mail: {$email}";
        echo "\n  Password: {$password}";
        echo "\n\n";

        return json_decode(json_encode([
            "email" => $email,
            "password" => $hash
        ]));
    }
}