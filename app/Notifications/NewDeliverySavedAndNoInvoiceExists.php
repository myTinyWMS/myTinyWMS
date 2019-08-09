<?php

namespace Mss\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Mss\Models\Delivery;

class NewDeliverySavedAndNoInvoiceExists extends Notification
{
    use Queueable;

    /**
     * @var Delivery
     */
    protected $delivery;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Neue Lieferung ohne Rechnung')
                    ->line('Zur Bestellung '.$this->delivery->order->internal_order_number.' ist eine Lieferung eingegangen.')
                    ->action('Bestellung anzeigen', route('order.show', $this->delivery->order))
                    ->line('Bitte prüfen!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order' => $this->delivery->order_id
        ];
    }
}
