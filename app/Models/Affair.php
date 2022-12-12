<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Affair extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'points',
        'active',
        'group_id',
        'user_id'
    ];

    public function check(){
        return $this->hasOne(Check::class)->whereDay('date', Carbon::today());
    }
    public function checkToday(){
        return $this->hasOne(Check::class);
    }
    public function checks(){
        return $this->hasMany(Check::class);
    }
}
