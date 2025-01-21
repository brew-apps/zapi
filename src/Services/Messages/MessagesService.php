<?php

namespace Brew\Zapi\Services\Messages;

use Brew\Zapi\Contracts\Messages\SendTextMessageInterface;
use Brew\Zapi\DTO\Messages\MessageResponseData;
use Brew\Zapi\DTO\Messages\TextMessageData;
use Brew\Zapi\Services\Base\ZapiService;

class MessagesService extends ZapiService implements SendTextMessageInterface
{
    public function sendText(TextMessageData $messageData): MessageResponseData
    {
        $response = $this->request('post', 'send-text', $messageData->toArray());

        return MessageResponseData::fromArray($response);
    }
}
