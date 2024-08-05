<?php namespace Notsoweb\LaravelMongoDB\Permission;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

 /**
 * Proveedor de servicio
 * 
 * Permite registrar el paquete dentro de laravel para usar funciones precargadas.
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Versión de aplicación
     */
    const VERSION = '0.0.1';

    /**
     * Acciones al iniciar servicio
     */
    public function boot() : void
    {
        $this->offerPublishing();
        $this->registerAbout();
    }

    /**
     * Registrar servicios
     */
    public function register() : void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/permission.php', 'permission'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/logging/channels.php', 'logging.channels'
        );

    }

    /**
     * Elementos que pueden publicarse
     */
    public function offerPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/permission.php' => config_path('permission.php'),
        ], 'permission-config');
    }

    /**
     * Retorna la ruta de la migración
     * 
     * Agrega el timestamp típico de las migraciones.
     */
    protected function migrationPath(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        return database_path("/migrations/{$timestamp}_{$migrationFileName}");
    }

    /**
     * Detalles del paquete
     */
    public function registerAbout() : void
    {
        AboutCommand::add('Notsoweb\\Permission', fn () => [
            'Version' => InstalledVersions::getPrettyVersion('notsoweb/laravel-mongodb-permission')
        ]);
    }
}