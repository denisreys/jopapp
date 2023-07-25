<?php

namespace Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    public $timestamps = false;
    
    use HasFactory;

    protected $fillable = [
        'task_id',
        'date',
        'points'
    ];

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
