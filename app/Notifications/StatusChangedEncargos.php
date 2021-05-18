<?php
/**
 * File name: StatusChangedEncargo.php
 * Last modified: 2020.04.29 at 10:35:47
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Notifications;

use App\Models\Encargo;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedEncargos extends Notification
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
            'title' => 'Tú Mandadito fue completado',
            'body' => 'Gracias por confiar en nosotros.',
            // 'text' => $this->encargo->productOrders[0]->product->market->name,
            // 'image' => $this->encargo->productOrders[0]->product->market->getFirstMediaUrl('image', 'thumb')
        ];
        $data = [
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'sound' => 'default',
            'id' => 'orders',
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
            'encargo_id' => $this->encargo['id'],
        ];
    }
}
