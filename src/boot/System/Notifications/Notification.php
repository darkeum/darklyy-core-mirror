<?php

namespace Boot\System\Notifications;

use Illuminate\Queue\SerializesModels;

/**
 * Notification
 * @method object toViber()
 * @method object toTelegram()
 * @method object toMail()
 */
class Notification
{
    use SerializesModels;

    /**
     * The unique identifier for the notification.
     *
     * @var string
     */
    public $id;

    /**
     * The locale to be used when sending the notification.
     *
     * @var string|null
     */
    public $locale;

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * Set the locale to send this notification in.
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function via($notifiable)
    {
        return [];
    } 
    
}
