<?php

namespace Brew\Zapi\Exceptions;

use Exception;

class ZapiException extends Exception
{
    protected array $response;

    public function __construct(
        string $message,
        int $code = 0,
        array $response = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Returns the raw API response that caused this exception.
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Create a new exception instance from an API response.
     */
    public static function fromResponse(array $response, int $statusCode = 0): self
    {
        $message = $response['message']
            ?? $response['error']
            ?? 'An error occurred with the Z-API service';

        return new static($message, $statusCode, $response);
    }

    /**
     * Create a new exception instance for authentication errors.
     */
    public static function authenticationError(array $response = []): self
    {
        return new static(
            'Invalid Z-API credentials. Please check your Instance ID, Instance Token and Client Token.',
            401,
            $response
        );
    }

    /**
     * Create a new exception instance for rate limit errors.
     */
    public static function rateLimitExceeded(array $response = []): self
    {
        return new static(
            'Z-API rate limit exceeded. Please try again later.',
            429,
            $response
        );
    }

    /**
     * Create a new exception instance for server errors.
     */
    public static function serverError(array $response = []): self
    {
        return new static(
            'Z-API server error occurred. Please try again later.',
            500,
            $response
        );
    }

    /**
     * Create a new exception instance for connection errors.
     */
    public static function connectionError(\Throwable $previous): self
    {
        return new static(
            'Could not connect to Z-API service: '.$previous->getMessage(),
            0,
            [],
            $previous
        );
    }
}
