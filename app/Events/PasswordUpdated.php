<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user, $oldpassword, $newpassword;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $oldpassword, $newpassword)
    {
        $this->user = $user;
        $this->oldpassword = $oldpassword;
        $this->newpassword = $newpassword;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
