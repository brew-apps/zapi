<?php

namespace Brew\Zapi\Contracts\Base;

interface ZapiRequestInterface
{
    public function request(string $method, string $endpoint, array $data = []): array;
}
