<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;
    protected $table = 'classrooms';
    protected $fillable = ['building_id', 'room_type_id', 'name'];
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
    public function roomType()
    {
        return $this->belongsTo(RoomType::class,'room_type_id');
    }
    public function equipmentClassrooms()
    {
        return $this->hasMany(EquipmentClassroom::class);
    }
    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class);
    }
}
