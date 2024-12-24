<?php

namespace Modules\Stadium\Entities;

// Importing necessary classes for the model
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a Stadium entity with attributes and relationships.
 */
class Stadium extends Model
{
    use HasFactory;

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
