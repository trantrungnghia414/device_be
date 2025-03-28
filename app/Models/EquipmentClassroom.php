<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentClassroom extends Model
{
    use HasFactory;
    protected $table = 'equipment_classrooms';
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    
}
