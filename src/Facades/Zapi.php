<?php

namespace Brew\Zapi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Brew\Zapi\DTO\Messages\MessageResponseData sendText(\Brew\Zapi\DTO\Messages\TextMessageData $messageData)
 */
class Zapi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zapi';
    }
}
