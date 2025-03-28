<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * 
     */
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',   
        'role_id',
        'repair_team_id',
        'status'
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
      
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function repairTeam()
    {
        return $this->belongsTo(RepairTeam::class);
    }
    public function assignmentUsers()
    {
        return $this->hasMany(AssignmentUser::class);
    }

    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class);
    }
   
}
