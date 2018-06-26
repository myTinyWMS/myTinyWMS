<?php

namespace Mss\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupplierMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Collection
     */
    protected $attachmentsList;

    /**
     * @var string
     */
    protected $body;

    protected $username;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $attachments = null) {
        $this->subject($subject);

        $this->body = $body;
        $this->attachmentsList = $attachments ?? collect([]);
        $this->username = Auth::user()->name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $this->attachmentsList->each(function ($attachment) {
            $this->attach(storage_path('app/attachments/'.$attachment['fileName']), [
                'as' => $attachment['orgFileName'],
                'mime' => $attachment['contentType']
            ]);
        });

        return $this->from('mail@example.com', $this->username)->view('emails.blank', ['content' => $this->body]);
    }
}
