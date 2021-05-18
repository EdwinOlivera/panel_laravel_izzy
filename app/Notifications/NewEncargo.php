<?php

namespace App\Notifications;

use App\Models\Encargo;
use FontLib\Table\Type\name;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Benwilkins\FCM\FcmMessage;

class NewEncargo extends Notification
{
    use Queueable;
    /**
     * @var Encargo
     */
    private $encargo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Encargo $encargo)
    {
        //
        $this->encargo = $encargo;
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
            'title'        => "Nuevo Mandadito #".$this->encargo->id." hacia ".$this->encargo->direccion_b,
            'body'         =>  "Dirección A: ".$this->encargo->direccion_a.", Dirección B: ".$this->encargo->direccion_b,

            // 'icon'         => $this->encargo->productOrders[0]->product->market->getFirstMediaUrl('image', 'thumb'),
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => 'Pedidos',
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
            'order_id' => $this->encargo['id'],
        ];
    }
}
