<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Faker\Provider\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mss\Models\Order;
use Mss\Models\OrderMessage;
use Mss\Models\Supplier;
use Mss\Services\ImportMailsService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webklex\IMAP\Client;
use Webklex\IMAP\Folder;
use Webklex\IMAP\Message;

class ImportMailsServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        factory(Supplier::class)->create();
    }

    /**
     * @test
     */
    public function is_creating_assigned_order_message_with_number_in_subject() {
        $order = factory(Order::class)->create();
        $service = $this->getFakeServiceWithFakeMessage('foo bar '.$order->internal_order_number);
        $service->process();

        $this->assertEquals(1, OrderMessage::where('subject', 'foo bar '.$order->internal_order_number)->count());
        $this->assertEquals($order->id, OrderMessage::where('subject', 'foo bar '.$order->internal_order_number)->first()->order_id);
    }

    /**
     * @test
     */
    public function is_creating_assigned_order_message_with_wrong_number_in_subject() {
        $order = factory(Order::class)->create();
        $service = $this->getFakeServiceWithFakeMessage('foo bar '.substr($order->internal_order_number, 0, -1));
        $service->process();

        $this->assertEquals(0, OrderMessage::where('subject', 'foo bar '.$order->internal_order_number)->count());
    }

    /**
     * @test
     */
    public function is_creating_assigned_order_message_with_number_in_html() {
        $order = factory(Order::class)->create();
        $service = $this->getFakeServiceWithFakeMessage('my subject2', 'html '.$order->internal_order_number.' html');
        $service->process();

        $this->assertEquals(1, OrderMessage::where('subject', 'my subject2')->count());
        $this->assertEquals($order->id, OrderMessage::where('subject', 'my subject2')->first()->order_id);
    }

    /**
     * @test
     */
    public function is_creating_assigned_order_message_with_number_in_text() {
        $order = factory(Order::class)->create();
        $service = $this->getFakeServiceWithFakeMessage('my subject3', 'html', 'text '.$order->internal_order_number.' text');
        $service->process();

        $this->assertEquals(1, OrderMessage::where('subject', 'my subject3')->count());
        $this->assertEquals($order->id, OrderMessage::where('subject', 'my subject3')->first()->order_id);
    }

    /**
     * @test
     */
    public function is_creating_unassigned_order_message_with_no_number() {
        $service = $this->getFakeServiceWithFakeMessage('my subject4');
        $service->process();

        $this->assertEquals(1, OrderMessage::where('subject', 'my subject4')->count());
        $this->assertNull(OrderMessage::where('subject', 'my subject4')->first()->order_id);
    }

    /**
     * @test
     */
    public function is_creating_unassigned_order_message_with_attachment() {
        Storage::fake('local');

        $attachment = new \stdClass();
        $attachment->content = 'foo';
        $attachment->content_type = 'image/jpg';
        $attachment->name = 'test.jpg';

        $attachments = [$attachment];
        $service = $this->getFakeServiceWithFakeMessage('my subject5', 'html', 'text', $attachments);
        $service->process();

        $this->assertTrue(file_exists(storage_path('attachments/'.OrderMessage::where('subject', 'my subject5')->first()->attachments[0]['fileName'])));
        $this->assertEquals(1, OrderMessage::where('subject', 'my subject5')->count());
        $this->assertNull(OrderMessage::where('subject', 'my subject5')->first()->order_id);
    }

    /**
     * @param string $subject
     * @param string $html
     * @param string $text
     * @return ImportMailsService
     */
    protected function getFakeServiceWithFakeMessage($subject = 'subject', $html = 'html', $text = 'text', $attachments = []) {
        $message = $this->createMock(Message::class);
        $message->from = [[
            'email' => 'foo@example.com'
        ]];
        $message->date = Carbon::now();
        $message->subject = $subject;
        $message->method('getHTMLBody')->willReturn($html);
        $message->method('getTextBody')->willReturn($text);
        $message->method('getAttachments')->willReturn(collect($attachments));

        $messages = [$message];
        $clientMock = $this->createMock(Client::class);

        $folderMock = $this->createMock(Folder::class);
        $folderMock->method('getMessages')->willReturn($messages);

        $clientMock->method('getFolder')->willReturn($folderMock);

        return new ImportMailsService($clientMock);
    }
}