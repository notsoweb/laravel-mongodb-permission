<?php namespace Notsoweb\LaravelMongoDB\Permission\Traits;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use Exception;
use MongoDB\Laravel\Relations\BelongsTo;
use Notsoweb\LaravelMongoDB\Permission\Models\Role;

/**
 * Traits para usuarios
 * 
 * Agrega las funciones requeridas en el modelo de usuarios
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
trait HasRoles
{
    private $roleName;

    # Relaciones

    /**
     * Un usuario puede pertenecer a muchos roles
     */
    public function role() : BelongsTo
    {
        return $this->belongsTo(Role::class)
            ->select('name', 'description');
    }

    # Acciones

    /**
     * Asignar role a usuario
     */
    public function assignRole($role)
    {
        if(!($role instanceof Role)) {
            if(is_string($role)) {
                $role = Role::whereName($role)->orWhere('_id', $role)->firstOrFail();
            } else {
                throw new Exception("No es un rol", 1);
            }
        }

        $this->update([
            'permissions' => $role->permissions->pluck('code')->toArray(),
            'role_id' => $role->id
        ]);
    }

    /**
     * Quitar rol
     */
    public function removeRole()
    {
        $this->update([
            'permissions' => [],
            'role_id' => null
        ]);
    }

    /**
     * Revisar si el usuario tiene un role específico
     */
    public function hasRoleName($roleName)
    {
        if(!$this->roleName) {
            $this->roleName = $this->role?->name;
        }

        return $this->roleName === $roleName;
    }
    
    /**
     * Verificar si el usuario tiene un permiso
     */
    public function can($abilities, $arguments = [])
    {
        if(!is_array($abilities)) {
            if(strpos($abilities, '|')) {
                return $this->can(explode('|', $abilities), $arguments);
            }

            return in_array($abilities, $this->permissions ?? []);
        }

        foreach ($abilities as $ability) {
            if ($this->can($ability)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si el usuario tiene al menos un role determinado
     */
    public function hasRole($roles)
    {
        if(!$this->roleName) {
            $this->roleName = $this->role?->name;
        }

        if(is_array($roles)) {
            return in_array($this->roleName, $roles);
        }

        if(strpos($roles, '|')) {
            return in_array($this->roleName, $roles);
        }

        return $roles == $this->roleName;
    }
}