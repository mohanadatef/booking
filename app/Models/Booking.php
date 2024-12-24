<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Modules\Stadium\Entities\Pitch;

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

    public function pitch()
    {
        return $this->belongsTo(Pitch::class);
    }
}
