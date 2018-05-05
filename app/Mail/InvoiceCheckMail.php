<?php

namespace Mss\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mss\Models\Order;

class InvoiceCheckMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $note;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var array
     */
    public $mail_attachments;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, $note, $attachments = [])
    {
        $this->order = $order;
        $this->note = $note;
        $this->mail_attachments = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('emails.invoice_check')
            ->subject('Rechnung muss überprüft werden');

        if (count($this->mail_attachments)) {
            foreach($this->mail_attachments as $attachment) {
                $mail->attach(storage_path('app/'.$attachment['tempFile']), [
                    'as' => $attachment['orgName'],
                    'mime' => $attachment['type']
                ]);
            }
        }

        return $mail;
    }
}
