<?php

namespace App\Notifications;

use App\Models\Order;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedOrder extends Notification
{
    use Queueable;

    /**
     * @var Order
     */
    private $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        //
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'fcm'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('¡Gracias por usar nuestra aplicación!');
    }

    public function toFcm($notifiable)
    {
        $message = new FcmMessage();
        $notification = [
            'title' => "Order #" . $this->order->id . " de " . $this->order->productOrders[0]->product->market->name . " Se te asigno",
            'text' => $this->order->productOrders[0]->product->name,
            'image' => $this->order->productOrders[0]->product->market->getFirstMediaUrl('image', 'thumb'),
            'icon' => $this->order->productOrders[0]->product->market->getFirstMediaUrl('image', 'thumb'),
        ];
        $data = [
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => 'Pedidos',
            'status' => 'done',
            'current_order_id' => $this->order->id,
            'message' => $notification,
        ];
        $message->content($notification)->data($data)->priority(FcmMessage::PRIORITY_HIGH);

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'current_order_id' => $this->order->id,
            'order_id' => $this->order['id'],
        ];
    }
}
