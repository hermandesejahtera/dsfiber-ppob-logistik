<?php

namespace DSFiber\Infrastructure\Biteship;

/**
 * Biteship API Client
 */
class Client
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = env('BITESHIP_URL', 'https://api.biteship.com');
        $this->apiKey = env('BITESHIP_API_KEY', '');
        $this->timeout = (int) env('BITESHIP_TIMEOUT', 30);
    }

    public function createWaybill(array $payload): array
    {
        $url = rtrim($this->baseUrl, '/') . '/waybills';
        return $this->request('POST', $url, $payload);
    }

    public function getWaybill(string $waybillNumber): array
    {
        $url = rtrim($this->baseUrl, '/') . '/waybills/' . urlencode($waybillNumber);
        return $this->request('GET', $url);
    }

    private function request(string $method, string $url, array $payload = []): array
    {
        $curl = curl_init();
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if ($method !== 'GET') {
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
            return ['success' => false, 'message' => 'Invalid Biteship response', 'status' => $status];
        }

        return $decoded;
    }
}
