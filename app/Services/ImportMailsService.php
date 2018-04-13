<?php

namespace Mss\Services;

use Illuminate\Support\Facades\Storage;
use Mss\Models\Order;
use Mss\Models\OrderMessage;
use Webklex\IMAP\Facades\Client;
use Webklex\IMAP\Message;
use Webpatser\Uuid\Uuid;

class ImportMailsService {

    /**
     * @var \Webklex\IMAP\Client
     */
    protected $client;

    /**
     * ImportMailsService constructor.
     * @param \Webklex\IMAP\Client $client
     */
    public function __construct(\Webklex\IMAP\Client $client) {
        $this->client = $client;
    }

    public function process() {
        $this->client->connect();

        /** @var \Webklex\IMAP\Folder $oFolder */
        $oFolder = $this->client->getFolder('INBOX');

        //Get all Messages
        /** @var \Webklex\IMAP\Message $message */
        foreach($oFolder->getMessages() as $message) {
            $order = $this->getOrderFromMessage($message);

            if ($order) {
                $order->messages()->create($this->getOrderMessageData($message));
            } else {
                OrderMessage::create($this->getOrderMessageData($message));
            }

            $message->delete();
        }
    }

    protected function getOrderMessageData(Message $message) {
        return [
            'sender' => collect($message->from)->pluck('mail'),
            'receiver' => ['System'],
            'received' => $message->date,
            'subject' => $message->subject,
            'htmlBody' => $message->getHTMLBody(),
            'textBody' => $message->getTextBody(),
            'attachments' => $message->getAttachments()->map(function ($attachment) {
                $fileName = Uuid::generate(4)->string;
                Storage::put('attachments/'.$fileName, $attachment->content);
                return [
                    'fileName' => $fileName,
                    'contentType' => $attachment->content_type,
                    'orgFileName' => $attachment->name
                ];
            })
        ];
    }

    /**
     * @param Message $message
     * @return Order|null
     */
    protected function getOrderFromMessage(Message $message) {
        $subject = $message->subject;
        $htmlContent = $message->getHTMLBody(true);
        $textContent = $message->getTextBody();

        foreach(compact('subject', 'htmlContent', 'textContent') as $content) {
            if (preg_match('/([0-9]{8})/', $content, $matches)) {
                $order = Order::where('internal_order_number', $matches[1])->first();
                if ($order) {
                    return $order;
                }
            }
        }

        return null;
    }

}