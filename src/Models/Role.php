<?php namespace Notsoweb\LaravelMongoDB\Permission\Models;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Roles del sistema
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
class Role extends BaseModel
{
    /**
     * Atributos permitidos
     */
    protected $fillable = [
        'name',
        'description'
    ];

     /**
     * Constructor
     */
    public function __construct(array $attributes = []) {
        $this->connection = config('permission.connection');
        $this->table = config('permission.collections.roles');

        parent::__construct($attributes);
    }

    /**
     * Un rol puede pertenecer a muchos permisos
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Un rol puede pertenecer a muchos usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    # Acciones

    /**
     * Buscar por nombre
     */
    public static function findByName($name)
    {
        return static::where('name', $name)->first();
    }

    /**
     * Verificar si el rol tiene un permiso
     */
    public function hasPermissionTo($permission)
    {
        return $this->permissions->contains('name', $permission);
    }

    /**
     * Agregar permiso
     */
    public function givePermissionTo($permissions)
    {
        $this->permissions()->attach(
            $this->preparePermissions($permissions)
        );

        $this->updateUsers();
    }

    /**
     * Remplazar todos los permisos
     */
    public function replacePermissions($permissions)
    {
        $this->permissions()->sync(
            $this->preparePermissions($permissions)
        );

        $this->updateUsers();
    }

    /**
     * Revocar todo los permisos
     */
    public function revokeAllPermissions()
    {
        $this->permissions()->detach();

        $this->updateUsers();
    }

    /**
     * Quitar permiso
     */
    public function revokePermissionTo($permissions)
    {
        $this->permissions()->detach(
            $this->preparePermissions($permissions)
        );

        $this->updateUsers();
    }

    /**
     * Eliminar rol
     */
    public function delete()
    {
        // Eliminar rol y permisos del usuario
        User::where('role_id', $this->_id)->update([
            'role_id' => null,
            'permissions' => []
        ]);

        // Eliminar relación de permisos con roles
        Permission::where('role_ids', $this->_id)
            ->pull('role_ids', $this->_id);

        // Eliminar
        DB::connection($this->connection)
            ->table($this->table)
            ->delete($this->_id);
    }

    /**
     * Actualizar usuarios con el rol actualizado
     */
    public function updateUsers()
    {
        User::where('role_id', $this->_id)->update([
            'permissions' => $this->permissions->pluck('code')->toArray()
        ]);
    }

    # Métodos privados

    /**
     * Preparar permisos para sincronización
     */
    private function preparePermissions($permissions)
    {
        if (!is_array($permissions)) {
            if(!($permissions instanceof Permission)) {
                if(strpos($permissions, '|')) {
                    return $this->preparePermissions(explode('|', $permissions));
                }

                $permissions = Permission::whereCode($permissions)
                    ->orWhere('_id', $permissions)
                    ->pluck('_id')
                    ->firstOrFail();
            }

            return $permissions->_id;
        } else {
            $items = [];

            foreach ($permissions as $permission) {
                $items[] = $this->preparePermissions($permission);
            }

            return $items;
        }
    }
}