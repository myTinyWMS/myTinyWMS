<?php

namespace Mss\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;
use Mss\Models\Delivery;

class NewDeliverySavedForWatchedCategories extends Notification
{
    use Queueable;

    /**
     * @var Collection
     */
    protected $articles;

    /**
     * @var Delivery
     */
    protected $delivery;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Collection $articles, Delivery $delivery)
    {
        $this->articles = $articles;
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
        return ['mail'];
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
                    ->subject('Neue Lieferung zu beobachteten Kategorien')
                    ->view('emails.new_delivery_for_watched_categories', ['articles' => $this->articles, 'delivery' => $this->delivery]);
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
