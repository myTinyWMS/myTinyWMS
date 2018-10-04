<?php

namespace Mss\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Mss\Models\Article;

class NewCorrectionForChangeFromDifferentMonth extends Notification
{
    use Queueable;

    protected $article;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
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
                    ->subject('Neue Korrektur zu Buchung aus anderem Monat')
                    ->line('Für den Artikel "'.$this->article->name.'" wurde eine Korrekturbuchung zu einem WE/WA aus einem anderen Monat erstellt.')
                    ->action('Artikel anzeigen', route('article.show', $this->article))
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
            'article' => $this->article->id
        ];
    }
}
