<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

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

    public $timestamps = true;

    public function pitches()
    {
        return $this->hasMany(Pitch::class);
    }
}
