<?php

namespace App\Modules\User\Events;

use App\Events\Event;
use App\Models\Db\User;
use Illuminate\Queue\SerializesModels;

class ActivationTokenWasRequested extends Event
{
    use SerializesModels;

    /**
     * User that was activated.
     *
     * @var User
     */
    public $user;

    /**
     * @var
     */
    public $url;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string $url
     */
    public function __construct(User $user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
