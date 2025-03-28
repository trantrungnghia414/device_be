<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class IncidentReported
{
    use Dispatchable, InteractsWithSockets;

    public $incident;
    public $user;
    public $classroom;

    public function __construct($incident, $user, $classroom)
    {
        $this->incident = $incident;
        $this->user = $user;
        $this->classroom = $classroom;
    }
}