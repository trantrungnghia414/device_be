<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;
    protected $table = 'incident_reports';
    protected $fillable = [
        'user_id',
        'classroom_id',
        'description',
        'status',
        'report_time'
    ];
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function assignment()
    // {
    //     return $this->hasMany(Assignment::class, 'incident_reports_id');
    // }
    public function assignment()
{
    return $this->hasOne(Assignment::class, 'incident_reports_id');
}

}
