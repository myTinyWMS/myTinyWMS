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
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $fileContent;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var Carbon
     */
    protected $date;

    /**
     * InventoryMail constructor.
     * @param Carbon $date
     * @param string $fileContent
     * @param string $mimeType
     * @param string $fileName
     */
    public function __construct(Carbon $date, $fileContent, $mimeType, $fileName)
    {
        $this->fileContent = $fileContent;
        $this->mimeType = $mimeType;
        $this->fileName = $fileName;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.blank', ['content' => 'siehe Anhang'])
            ->subject('Inventur '.$this->date->format('Y-m-d'))
            ->attachData($this->fileContent, $this->fileName, ['mime' => $this->mimeType]);
    }
}
