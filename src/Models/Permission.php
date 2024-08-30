<?php namespace Notsoweb\LaravelMongoDB\Permission\Models;
/**
 * @copyright 2024 Notsoweb (https://notsoweb.com) - All rights reserved.
 */

use App\Models\User;
use Illuminate\Support\Facades\DB;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;

/**
 * Roles del sistema
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
class Permission extends BaseModel
{
    /**
     * Atributos permitidos
     */
    protected $fillable = [
        'code',
        'description',
        'permission_type_id'
    ];

    /**
     * Constructor
     */
    public function __construct(array $attributes = []) {
        $this->connection = config('permission.connection');
        $this->table = config('permission.collections.permissions');
        
        parent::__construct($attributes);
    }

    /**
     * Get the attributes that should be cast.
     */
    public function  casts()
    {
        return [
            'permission_type_id' => ObjectId::class
        ];
    }

    /**
     * Un permiso pertenece a un tipo de permiso
     */
    public function permissionType()
    {
        return $this->belongsTo(PermissionType::class);
    }

    /**
     * Un permiso puede pertenecer a muchos roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Eliminar permiso
     */
    public function delete()
    {
        // Eliminar permiso de los usuarios
        User::where('permissions', $this->code)
            ->pull('permissions', $this->code);

        // Eliminar permiso de los roles
        Role::where('permission_ids', $this->_id)
            ->pull('permission_ids', $this->_id);

        // Eliminar
        DB::connection($this->connection)
            ->table($this->table)
            ->delete($this->_id);
    }

    # Acciones

    /**
     * Buscar por código
     */
    public static function findByCode($name)
    {
        return static::where('code', $name)->first();
    }
}