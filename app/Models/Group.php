<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name',
        'icon',
        'color',
        'user_id',
        'active'
    ];

    public function affairs(){
        return $this->hasMany(Affair::class)->where(['active' => 1, 'state' => 2]);
    }
}
