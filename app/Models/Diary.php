<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
    use HasFactory;

    public function affair(){
        return $this->belongsTo(Affair::class);
    }
    /*public function affair(){
        return $this->hasManyThrough(
            Affair::class,
            Check::class,
            'id', // Local key on the mechanics table...
            'id', // Local key on the cars table...
            'affair_id', // Foreign key on the cars table...
            'affair_id', // Foreign key on the owners table...
        );
    }*/
}
