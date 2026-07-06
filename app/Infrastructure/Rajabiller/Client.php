<?php

namespace DSFiber\Infrastructure\Rajabiller;

/**
 * Rajabiller API Client
 */
class Client
{
    private string $baseUrl;
    private string $apiKey;
    private string $username;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = env('RAJABILLER_URL', 'https://api.rajabiller.com');
        $this->apiKey = env('RAJABILLER_KEY', '');
        $this->username = env('RAJABILLER_USERNAME', '');
        $this->timeout = (int) env('RAJABILLER_TIMEOUT', 30);
    }

    public function fetchProducts(): array
    {
        $url = rtrim($this->baseUrl, '/') . '/products';
        $response = $this->request('GET', $url);
        return $response['data'] ?? [];
    }

    public function getProductDetails(string $externalId): ?array
    {
        $url = rtrim($this->baseUrl, '/') . '/products/' . urlencode($externalId);
        $response = $this->request('GET', $url);
        return $response['data'] ?? null;
    }

    private function request(string $method, string $url, array $payload = []): array
    {
        $curl = curl_init();
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->apiKey,
            'X-API-USER: ' . $this->username,
        ];

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $result = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error) {
            return ['success' => false, 'message' => $error, 'status' => $status];
        }

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            return ['success' => false, 'message' => 'Invalid Rajabiller response', 'status' => $status];
        }

        return $decoded;
    }
}
