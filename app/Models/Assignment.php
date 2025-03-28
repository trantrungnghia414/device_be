<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $table = 'assignments';
  public function incidentReport()
    {
        return $this->belongsTo(IncidentReport::class, 'incident_reports_id');
    }

   public function assignmentUsers()
   {
    return $this->hasMany(AssignmentUser::class);
   }
}
