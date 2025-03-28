<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairTeam extends Model
{
    use HasFactory;
    protected $table = 'repair_teams';
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
