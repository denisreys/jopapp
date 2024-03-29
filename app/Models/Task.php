<?php

namespace Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'points',
        'active',
        'group_id',
        'user_id',
        'state'
    ];

    public function check(){
        return $this->hasOne(Check::class)->whereDate('date', Carbon::today());
    }
    public function checkToday(){
        return $this->hasOne(Check::class);
    }
    public function checkWeek(){
        return $this->hasOne(Check::class)->whereDate('date', '>=', Carbon::now()->subWeek());
    }
    public function checkLastWeek(){
        return $this->hasOne(Check::class)->whereDate('date', '<=', Carbon::now()->subWeek());
    }
    public function checks(){
        return $this->hasMany(Check::class);
    }
    public function todo(){
        return $this->hasOne(Todo::class);
    }
    public function group(){
        return $this->belongsTo(Group::class);
    }
}
