<?php

namespace Brew\Zapi\Tests\Feature\Messages;

use Brew\Zapi\DTO\Messages\TextMessageData;
use Brew\Zapi\Exceptions\ZapiException;
use Brew\Zapi\Facades\Zapi;
use Brew\Zapi\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class SendTextMessageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_can_send_text_message_successfully()
    {
        // Arrange
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'messageId' => '123456',
                'status' => 'sent',
                'message' => 'Message sent successfully',
            ], 200),
        ]);

        $messageData = new TextMessageData(
            phone: '5511999999999',
            message: 'Test message',
            delayMessage: 1
        );

        // Act
        $response = Zapi::sendText($messageData);

        // Assert
        $this->assertTrue($response->success);
        $this->assertEquals('123456', $response->messageId);
        $this->assertEquals('sent', $response->status->value);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Client-Token') &&
                str_contains($request->url(), 'send-text');
        });
    }

    #[Test]
    public function it_throws_exception_for_invalid_credentials()
    {
        // Arrange
        Http::fake([
            '*' => Http::response([
                'success' => false,
                'message' => 'Invalid Z-API credentials. Please check your Instance ID, Instance Token and Client Token.',
            ], 401),
        ]);

        $messageData = new TextMessageData(
            phone: '5511999999999',
            message: 'Test message'
        );

        // Act & Assert
        $this->expectException(ZapiException::class);
        $this->expectExceptionMessage('Invalid Z-API credentials. Please check your Instance ID, Instance Token and Client Token.');

        Zapi::sendText($messageData);
    }

    #[Test]
    public function it_logs_the_request_and_response()
    {
        // Arrange
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'messageId' => '123456',
                'status' => 'sent',
            ], 200),
        ]);

        $messageData = new TextMessageData(
            phone: '5511999999999',
            message: 'Test message'
        );

        // Act
        Zapi::sendText($messageData);

        // Assert
        $this->assertDatabaseHas('zapi_logs', [
            'endpoint' => 'send-text',
            'status_code' => 200,
        ]);
    }
}
