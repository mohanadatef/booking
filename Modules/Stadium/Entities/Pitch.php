<?php

namespace Modules\Stadium\Entities;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;

/**
 * The Pitch class represents a pitch entity within the system.
 * It defines the structure of the pitch and its relationships with
 * other entities, such as stadiums and bookings.
 */
class Pitch extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'stadium_id'
    ];

    public $timestamps = true;
    // This class appears to handle search configurations and relationships for a database or data model.
    public $searchRelationShip = [];
    /**
     * [columns that needs to has customed search such as like or where in]
     *
     * @var string[]
     */
    public $searchConfig = [];
    /**
     * Validation rules for creating or updating a pitch.
     * It specifies required fields and their constraints.
     */
    public static $rules = [
        'name' => 'required|string|unique:pitches',
        'stadium_id' => 'required|integer|exists:stadiums,id'
    ];

    /**
     * Retrieves the validation rules for the pitch model.
     *
     * @return array The validation rules.
     */
    public static function getValidationRules()
    {
        return self::$rules;
    }

    /**
     * Defines the relationship between a pitch and its corresponding stadium.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship instance.
     */
    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }

    /**
     * Defines the relationship between a pitch and its bookings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The relationship instance.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

