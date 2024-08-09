<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('New order received!')
            ->attachment(function ($attachment) {
                $attachment->title('Order #' . $this->order->id)
                    ->fields([
                        'Total Amount' => $this->order->total_amount,
                        'Status' => $this->order->status,
                    ]);
            });
    }
}
