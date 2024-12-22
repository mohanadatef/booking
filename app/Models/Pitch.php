<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Pitch extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name','stadium_id'
    ];

    public $timestamps = true;

    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
