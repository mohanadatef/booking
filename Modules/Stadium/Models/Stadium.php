<?php

namespace Modules\Stadium\Models;

// Importing necessary classes for the model
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a Stadium entity with attributes and relationships.
 */
class Stadium extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    // Specifies the database table associated with the model
    protected $table = 'stadiums';

    // Indicates if the model should be timestamped
    public $timestamps = true;
    // This class appears to handle search configurations and relationships for a database or data model.
    public $searchRelationShip = [];
    /**
     * [columns that needs to has customed search such as like or where in]
     *
     * @var string[]
     */
    public $searchConfig = [];

    // Validation rules for the Stadium entity
    public static $rules = [
        'name' => 'required|string|unique:stadiums',
    ];

    /**
     * Returns the validation rules for the Stadium entity.
     *
     * @return array
     */
    public static function getValidationRules()
    {
        return self::$rules;
    }

    /**
     * Defines the relationship between a Stadium and its Pitches.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pitches()
    {
        return $this->hasMany(Pitch::class);
    }
}

