<?php

namespace App\Services;

use App\Exceptions\KedrApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KedrService
{
    private string $baseUrl;
    private string $apiKey;

    private const ACCESS_PROTOCOL_URL = 'get-access-protocol.ashx';
    private const EMPLOYEES_URL = 'get-users-list.ashx';

    public function __construct()
    {
        $this->baseUrl = config('kedr.url');
        $this->apiKey = config('kedr.api_key');
    }

    /**
     * @param string|null $beg
     * @param string|null $end
     * @return array
     * @throws KedrApiException
     */
    public function getAccessProtocol(?string $beg = null, ?string $end = null): array
    {
        return $this->request(
            self::ACCESS_PROTOCOL_URL,
            [
                'beg_date' => $beg,
                'end_date' => $end,
            ],
            'events'
        );
    }

    /**
     * @return array
     * @throws KedrApiException
     */
    public function getEmployeesList(): array
    {
        return $this->request(self::EMPLOYEES_URL, [], 'items');
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param string $resultKey
     * @return array
     * @throws KedrApiException
     * @throws ConnectionException
     */
    private function request(string $endpoint, array $params, string $resultKey): array
    {
        $query = array_filter(array_merge(['apiKey' => $this->apiKey], $params));

        $response = Http::timeout(10)->get($this->makeUrl($endpoint), $query);

        if ($response->failed()) {
            $context = [
                'endpoint' => $endpoint,
                'params' => $query,
                'body' => $response->body(),
            ];
            $this->logError($endpoint, $context);
            throw new KedrApiException("Kedr API request failed: $endpoint", $context);
        }

        $json = $response->json();

        if (isset($json['error'])) {
            $context = [
                'endpoint' => $endpoint,
                'params' => $query,
                'error' => $json['error'],
            ];
            $this->logError($endpoint, $context);
            throw new KedrApiException("Kedr API returned error: {$json['error']}", $context);
        }

        return $json[$resultKey] ?? [];
    }

    /**
     * @param string $resource
     * @return string
     */
    private function makeUrl(string $resource): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($resource, '/');
    }

    /**
     * @param string $method
     * @param array $context
     * @return void
     */
    private function logError(string $method, array $context = []): void
    {
        Log::error("Kedr API $method failed", $context);
    }
}
