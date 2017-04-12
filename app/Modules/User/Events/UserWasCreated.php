<?php

namespace App\Modules\User\Events;

use App\Events\Event;
use App\Models\Db\User;
use Illuminate\Queue\SerializesModels;

class UserWasCreated extends Event
{
    use SerializesModels;

    /**
     * User that was created.
     *
     * @var User
     */
    public $user;

    /**
     * Activation url.
     *
     * @var string|null
     */
    public $url;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string|null $url
     */
    public function __construct(User $user, $url = null)
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
