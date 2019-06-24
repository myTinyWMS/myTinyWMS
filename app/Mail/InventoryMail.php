<?php

namespace Mss\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InventoryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Carbon
     */
    protected $date;

    /**
     * @var array
     */
    protected $files;

    /**
     * InventoryMail constructor.
     *
     * @param Carbon $date
     * @param array $files
     */
    public function __construct(Carbon $date, $files = [])
    {
        $this->files = $files;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('emails.blank', ['content' => 'siehe Anhang'])
            ->subject('Inventur '.$this->date->format('Y-m-d'));

        foreach($this->files as $attachment) {
            $mail->attachData($attachment[0], $attachment[2], ['mime' => $attachment[1]]);
        }

        return $mail;
    }
}
