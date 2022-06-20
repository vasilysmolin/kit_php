<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaveLogsEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $query;
    public $type;

    public function __construct(string $query, string $type, ?User $user)
    {
        $this->query = $query;
        $this->type = $type;
        $this->user = $user;
    }
}
