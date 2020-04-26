<?php

namespace Mss\Http\Controllers\Order;

use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;
use Mss\Models\Order;
use Mss\Models\OrderMessage;
use Webpatser\Uuid\Uuid;

class MessageAttachmentController extends Controller
{
    public function upload(Order $order, Request $request) {
        if (config('app.demo')) {
            return response()->json('error', 400);
        }

        $file = $request->file('file');

        /**
         * @todo queue file to delete after some time
         */
        $upload_success = $file->storeAs('upload_temp', $order->id.'_'.Uuid::generate(4)->string);
        if ($upload_success) {
            return response()->json($upload_success, 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function download(OrderMessage $message, $attachment) {
        $attachment = $message->attachments->where('fileName', $attachment)->first();
        $filePath = storage_path('attachments/'.$attachment['fileName']);
        if (!file_exists($filePath)) {
            $filePath = storage_path('app/attachments/'.$attachment['fileName']);
            if (!file_exists($filePath)) {
                return response(__("Datei nicht gefunden"), 404);
            }
        }

        return response()->download($filePath, $attachment['orgFileName'], ['Content-Type' => $attachment['contentType']]);
    }
}
