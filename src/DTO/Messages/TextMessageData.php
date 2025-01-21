<?php

namespace Brew\Zapi\DTO\Messages;

class TextMessageData
{
    public function __construct(
        public readonly string $phone,
        public readonly string $message,
        public readonly ?int $delayMessage = null
    ) {}

    public function toArray(): array
    {
        $data = [
            'phone' => $this->phone,
            'message' => $this->message,
        ];

        if ($this->delayMessage !== null) {
            $data['delayMessage'] = $this->delayMessage;
        }

        return $data;
    }
}
