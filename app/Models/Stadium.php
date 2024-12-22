<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    protected $table = 'stadiums';
    public $timestamps = true;

    public function pitches()
    {
        return $this->hasMany(Pitch::class);
    }
}
