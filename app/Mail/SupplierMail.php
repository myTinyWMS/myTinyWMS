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
            if (file_exists(storage_path('attachments/'.$attachment['fileName'])) && is_readable(storage_path('attachments/'.$attachment['fileName']))) {
                $this->attach(storage_path('attachments/'.$attachment['fileName']), [
                    'as' => $attachment['orgFileName'],
                    'mime' => $attachment['contentType']
                ]);
            } else {
                Log::error('Unable to attach file to Mail', ['attachment' => $attachment, 'mail' => $this]);
            }
        });

        return $this->from('mail@example.com', $this->username)->view('emails.blank', ['content' => $this->body]);
    }
}
