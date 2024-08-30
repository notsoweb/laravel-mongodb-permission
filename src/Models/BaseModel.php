<?php namespace Notsoweb\LaravelMongoDB\Permission\Models;
/**
 * @copyright 2024 Notsoweb Software - All rights reserved
 */

use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Auth\User;
use MongoDB\Laravel\Eloquent\DocumentModel;

use function array_key_exists;
use function class_uses_recursive;
use function is_object;
use function is_subclass_of;

/**
 * Modelo Base
 * 
 * Instancia modificada de MONGODB.
 * 
 * @author Moisés Cortés C. <moises.cortes@notsoweb.com>
 * 
 * @version 1.0.0
 */
abstract class BaseModel extends Model
{
    use DocumentModel;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = '_id';

    /**
     * The primary key type.
     *
     * @var string
     */
    protected $keyType = 'string';

    private static $documentModelClasses = [
        User::class => true,
    ];

    /**
     * Indicates if the given model class is a MongoDB document model.
     * It must be a subclass of {@see BaseModel} and use the
     * {@see DocumentModel} trait.
     *
     * @param class-string|object $class
     */
    final public static function isDocumentModel(string|object $class): bool
    {
        if (is_object($class)) {
            $class = $class::class;
        }

        if (array_key_exists($class, self::$documentModelClasses)) {
            return self::$documentModelClasses[$class];
        }

        // We know all child classes of this class are document models.
        if (is_subclass_of($class, self::class)) {
            return self::$documentModelClasses[$class] = true;
        }

        // Document models must be subclasses of Laravel's base model class.
        if (! is_subclass_of($class, BaseModel::class)) {
            return self::$documentModelClasses[$class] = false;
        }

        // Document models must use the DocumentModel trait.
        return self::$documentModelClasses[$class] = array_key_exists(DocumentModel::class, class_uses_recursive($class));
    }

    /**
     * Custom accessor for the model's id.
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    public function getIdAttribute($value = null)
    {
        // If we don't have a value for 'id', we will use the MongoDB '_id' value.
        // This allows us to work with models in a more sql-like way.
        if (! $value && array_key_exists('_id', $this->attributes)) {
            $value = $this->attributes['_id'];
        }

        // Convert ObjectID to string.
        if ($value instanceof ObjectID) {
            return (string) $value;
        }

        if ($value instanceof Binary) {
            return (string) $value->getData();
        }

        return $value;
    }
}
