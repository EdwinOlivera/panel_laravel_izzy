<?php

namespace App\Notifications;

use App\Models\Encargo;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedEncargo extends Notification
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
            'title' => "Encargo #" . $this->encargo->id . " de " . $this->encargo->user->name ." Se le ha asignado a usted",
            'text'         => $this->encargo->direccion_a,
            'current_order_id' => $this->encargo->id,

            // 'image' => $this->encargo->productOrders[0]->product->market->getFirstMediaUrl('image', 'thumb'),
            // 'icon' => $this->encargo->productOrders[0]->product->market->getFirstMediaUrl('image', 'thumb'),
        ];
        $data = [
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => '1',
            'status' => 'done',
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
            'current_order_id' => $this->encargo->id,

            'order_id' => $this->encargo['id'],
        ];
    }
}
