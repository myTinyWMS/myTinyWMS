<?php

namespace Mss\Services;

use Illuminate\Support\Facades\Storage;
use Mss\Models\Order;
use Mss\Models\OrderMessage;
use Webklex\IMAP\Facades\Client;
use Webklex\IMAP\Message;
use Webpatser\Uuid\Uuid;

class ImportMailsService {

    public function process() {
        /** @var \Webklex\IMAP\Client $oClient */
        $oClient = Client::account('default');
        $oClient->connect();

        /** @var \Webklex\IMAP\Folder $oFolder */
        $oFolder = $oClient->getFolder('INBOX');

        if (!is_dir(storage_path('attachments'))) {
            mkdir(storage_path('attachments'));
        }

        //Get all Messages
        /** @var \Webklex\IMAP\Message $message */
        foreach($oFolder->getMessages() as $message) {
            $order = $this->getOrderFromMessage($message);

            if ($order) {
                $order->messages()->create($this->getOrderMessageData($message));
            } else {
                OrderMessage::create($this->getOrderMessageData($message));
            }
        }
    }

    protected function getOrderMessageData(Message $message) {
        return [
            'sender' => collect($message->from)->pluck('mail'),
            'receiver' => ['System'],
            'subject' => $message->subject,
            'htmlBody' => $message->getHTMLBody(),
            'textBody' => $message->getTextBody(),
            'attachments' => $message->getAttachments()->map(function ($attachment) {
                $fileName = Uuid::generate(4)->string;
                file_put_contents(storage_path('attachments/'.$fileName), $attachment->content);
                return [
                    'fileName' => $fileName,
                    'contentType' => $attachment->content_type
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
            if (preg_match('/\{([0-9]{8})}/', $content, $matches)) {
                $order = Order::where('internal_order_number', $matches[1])->firstOrFail();
                if ($order) {
                    return $order;
                }
            }
        }

        return null;
    }

}