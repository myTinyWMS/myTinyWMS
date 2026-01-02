<?php

namespace Mss\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MinQuantitiesCalculation extends Mailable
{
    use Queueable, SerializesModels;

    public $items;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Collection $items) {
        $this->items = $items;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('Empfehlung Mindestbestände'))
            ->markdown('emails.min_quantities_calculation');
    }
}
