<?php

namespace App\Modules\User\Events;

use App\Events\Event;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserWasCreated extends Event
{
    use SerializesModels;

    /**
     * User that was created
     *
     * @var User
     */
    public $user;

    /**
     * Extra settings for this event
     *
     * @var array
     */
    public $settings;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param $settings
     */
    public function __construct(User $user, $settings)
    {
        $this->user = $user;
        $this->settings = $settings;
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

    /**
     * Checks whether notification to user about his account creation should be
     * sent
     *
     * @return bool
     */
    public function shouldSendUserNotification()
    {
        return $this->settings['send_user_notification'] ?? true;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->settings['url'] ?? null;
    }

    /**
     * Verify whether user created account himself or it was created by other
     * user
     *
     * @return bool
     */
    public function wasSelfCreated()
    {
        return $this->settings['creator_id'] == $this->user->id;
    }
}
