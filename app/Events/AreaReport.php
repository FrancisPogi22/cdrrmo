<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AreaReport implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function broadcastOn()
    {
        return new Channel('area-report');
    }
}
