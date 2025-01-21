<?php

namespace Brew\Zapi\DTO\Messages;

use Brew\Zapi\DTO\Common\ResponseData;
use Brew\Zapi\Enums\MessageStatus;

class MessageResponseData extends ResponseData
{
    public function __construct(
        public readonly bool $success,
        public readonly string $messageId,
        public readonly MessageStatus $status,
        public readonly ?string $message = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            messageId: $data['messageId'] ?? '',
            status: MessageStatus::from($data['status'] ?? 'error'),
            message: $data['message'] ?? null
        );
    }
}
