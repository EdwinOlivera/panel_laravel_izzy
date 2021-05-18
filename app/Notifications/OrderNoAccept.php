<?php

namespace App\Notifications;

use App\Models\Order;
use FontLib\Table\Type\name;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Benwilkins\FCM\FcmMessage;

class OrderNoAccept extends Notification
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
        return ['database','fcm'];
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
            'title'        => "Pedido #".$this->order->id." no fue tomado.",
            'body'         => 'Ningún repartidor tomó la orden, favor asignarla desde el dashboard.',
            'icon'         => $this->order->productOrders[0]->product->market->getFirstMediaUrl('image', 'thumb'),
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => '1',
            'status' => 'done',
        ];
        $message->content($notification)->data($notification)->priority(FcmMessage::PRIORITY_HIGH);

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
            'current_order_id'     => $this->order->id,
            'order_id' => $this->order['id'],
        ];
    }
}
