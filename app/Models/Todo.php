<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'todoes';
    protected $fillable = [
        'user_id',
        'date',
        'group_id',
        'name',
        'task_id'
    ];

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
