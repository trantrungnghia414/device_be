<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentUser extends Model
{
    use HasFactory;
    protected $table = 'assignment_users';
    protected $fillable = ['assignment_id', 'user_id', 'status', 'completion_time', 'notes'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
