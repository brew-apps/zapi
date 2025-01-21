<?php

namespace Brew\Zapi\Contracts\Messages;

use Brew\Zapi\DTO\Messages\MessageResponseData;
use Brew\Zapi\DTO\Messages\TextMessageData;

interface SendTextMessageInterface
{
    public function sendText(TextMessageData $messageData): MessageResponseData;
}
