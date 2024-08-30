<?php namespace Notsoweb\LaravelMongoDB\Permission\Models;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Tipos de permisos
 * 
 * Es una agrupación de permisos en base a una categoría.
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
class PermissionType extends BaseModel
{
    /**
     * Atributos permitidos
     */
    protected $fillable = [
        'prefix',
        'name',
        'description'
    ];

     /**
     * Constructor
     */
    public function __construct(array $attributes = []) {
        $this->connection = config('permission.connection');
        $this->table = config('permission.collections.permission_types');
        
        parent::__construct($attributes);
    }

    /**
     * Un tipo de permiso tiene muchos permisos
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * Eliminar rol
     */
    public function delete()
    {
        $permissions = $this->permissions;
        $codes = $permissions->pluck('code')->toArray();
        $ids = $permissions->pluck('_id')->toArray();
        
        // Eliminar permisos de usuarios
        User::whereIn('permissions', $codes)->pull(
            'permissions', [
                '$in' => $codes
            ]
        );

        // Eliminar permisos de roles
        Role::whereIn('permission_ids', $ids)->pull(
            'permission_ids', [
                '$in' => $ids
            ]
        );

        $this->permissions()->delete();
        
        // Eliminar
        DB::connection($this->connection)
            ->table($this->table)
            ->delete($this->_id);
    }

    # Acciones

    /**
     * Buscar por prefijo
     */
    public static function findByPrefix($prefix)
    {
        return static::where('prefix', $prefix)->first();
    }

    /**
     * Crear permiso
     */
    public function newPermission(string $code, string | null $description = null)
    {
        return $this->permissions()->firstOrCreate([
            'code' => "{$this->prefix}.{$code}",
        ], [
            'description' => $description ?? "{$this->name}: " . __("registers.{$code}")
        ]);
    }

    /**
     * Permiso para listar
     */
    public function newIndexPermission(string | null $description = null)
    {
        return $this->newPermission('index', $description);
    }

    /**
     * Permiso para crear
     */
    public function newCreatePermission(string | null $description = null)
    {
        return $this->newPermission('create', $description);
    }

    /**
     * Permiso para editar
     */
    public function newEditPermission(string | null $description = null)
    {
        return $this->newPermission('edit', $description);
    }

    /**
     * Permiso para eliminar
     */
    public function newDestroyPermission(string | null $description = null)
    {
        return $this->newPermission('destroy', $description);
    }

    /**
     * Permisos de un CRUD básico
     */
    public function newCRUDPermission($index = null, $create = null, $edit = null, $destroy = null)
    {
        return [
            $this->newIndexPermission($index),
            $this->newCreatePermission($create),
            $this->newEditPermission($edit),
            $this->newDestroyPermission($destroy),
        ];
    }
}