<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;
    protected $table = 'buildings';
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
