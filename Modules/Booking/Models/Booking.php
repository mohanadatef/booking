<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Stadium\Models\Pitch;

/**
 * Class Booking
 *
 * This class represents the booking model and contains the properties
 * and methods related to booking entities in the application, including
 * validation rules and relationships with other models such as Pitch.
 */
class Booking extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date','pitch_id','start_time','end_time'
    ];

    public $timestamps = true;
    public $searchRelationShip = [];
    /**
     * [columns that needs to has customed search such as like or where in]
     *
     * @var string[]
     */
    public $searchConfig = [];

    // Validation rules for the Stadium entity
    public static $rules = [
        'date' => 'required|date|date_format:Y-m-d', // Ensure correct date format
        'start_time' => 'required|date_format:H:i:s',
        'end_time' => 'required|date_format:H:i:s|after:start_time',
        'pitch_id' => 'required|exists:pitches,id'
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
     * Defines the relationship between Booking and Pitch models.
     *
     * This method establishes that a Booking belongs to a Pitch,
     * enabling retrieval of the associated pitch for the booking.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pitch()
    {
        return $this->belongsTo(Pitch::class);
    }
}

