<?php

namespace Brew\Zapi\Services\Base;

use Brew\Zapi\Contracts\Base\ZapiRequestInterface;
use Brew\Zapi\Exceptions\ZapiException;
use Brew\Zapi\Models\ZapiLog;
use Illuminate\Support\Facades\Http;

abstract class ZapiService implements ZapiRequestInterface
{
    protected string $apiUrl;

    protected string $instanceId;

    protected string $instanceToken;

    protected string $clientToken;

    public function __construct()
    {
        $this->apiUrl = config('zapi.api_url');
        $this->instanceId = config('zapi.instance_id');
        $this->instanceToken = config('zapi.instance_token');
        $this->clientToken = config('zapi.client_token');
    }

    public function request(string $method, string $endpoint, array $data = []): array
    {
        $url = "{$this->apiUrl}/instances/{$this->instanceId}/token/{$this->instanceToken}/{$endpoint}";
        $startTime = microtime(true);

        try {
            $response = Http::withHeaders([
                'Client-Token' => $this->clientToken,
                'Content-Type' => 'application/json',
            ])->$method($url, $data);

            $responseData = $response->json();
            $executionTime = microtime(true) - $startTime;

            $this->logRequest(
                endpoint: $endpoint,
                requestData: $data,
                responseData: $responseData,
                statusCode: $response->status(),
                executionTime: $executionTime
            );

            // Tratamento de erros específicos
            if ($response->status() === 401) {
                throw ZapiException::authenticationError($responseData);
            }

            if ($response->status() === 429) {
                throw ZapiException::rateLimitExceeded($responseData);
            }

            if ($response->status() >= 500) {
                throw ZapiException::serverError($responseData);
            }

            if (! $response->successful()) {
                throw ZapiException::fromResponse($responseData, $response->status());
            }

            return $responseData;
        } catch (\Exception $e) {
            if ($e instanceof ZapiException) {
                throw $e;
            }

            throw ZapiException::connectionError($e);
        }
    }

    protected function logRequest(
        string $endpoint,
        array $requestData,
        array $responseData,
        int $statusCode,
        float $executionTime
    ): void {
        try {
            ZapiLog::create([
                'endpoint' => $endpoint,
                'request_data' => $requestData,
                'response_data' => $responseData,
                'status_code' => $statusCode,
                'execution_time' => $executionTime,
            ]);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
