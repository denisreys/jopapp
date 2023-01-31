<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    public $timestamps = false;
    
    use HasFactory;

    protected $fillable = [
        'affair_id',
        'date',
        'points'
    ];

    public function affair(){
        return $this->belongsTo(Affair::class);
    }
}
