<?php

namespace Brew\Zapi\Tests\Unit\DTO\Messages;

use Brew\Zapi\DTO\Messages\TextMessageData;

test('can create text message data with minimum parameters', function () {
    $messageData = new TextMessageData(
        phone: '5511999999999',
        message: 'Test message'
    );

    expect($messageData)
        ->phone->toBe('5511999999999')
        ->message->toBe('Test message')
        ->delayMessage->toBeNull();

    $array = $messageData->toArray();
    expect($array)
        ->toHaveKey('phone')
        ->toHaveKey('message')
        ->not->toHaveKey('delayMessage');
});

test('can create text message data with all parameters', function () {
    $messageData = new TextMessageData(
        phone: '5511999999999',
        message: 'Test message',
        delayMessage: 1
    );

    expect($messageData->toArray())
        ->toHaveKey('phone')
        ->toHaveKey('message')
        ->toHaveKey('delayMessage')
        ->and($messageData->toArray()['delayMessage'])->toBe(1);
});
