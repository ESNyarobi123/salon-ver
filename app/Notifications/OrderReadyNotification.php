<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderReadyNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'table_number' => $this->order->table_number,
            'message' => 'Booking #'.$this->order->id.' is ready for seat '.$this->order->table_number,
            'type' => 'order_ready',
        ];
    }
}
