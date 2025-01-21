<?php

namespace Brew\Zapi\DTO\Common;

abstract class ResponseData
{
    abstract public static function fromArray(array $data): self;
}
