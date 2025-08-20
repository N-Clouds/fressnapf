<?php

namespace App\Services\Mirakl\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiClient
{
    private Client $client;
    private ?string $token = null;

    private function getClient(): Client
    {
        if (!isset($this->client)) {
            $this->client = new Client([
                'base_uri' => $this->baseUrl(),
            ]);
        }

        return $this->client;
    }

    private function baseUrl(): string
    {
        return config('fressnapf.host');
    }

    /**
     * Führt eine Anfrage aus.
     *
     * @param string     $httpMethod  'get','post',...
     * @param string     $url         Pfad oder komplette URL
     * @param array      $parameters  (optional) JSON-Body
     * @param array      $query       (optional) Query-Parameter
     * @return array|null
     */
    private function execute(string $httpMethod, string $url, array $parameters = [], array $query = []): ?array
    {
        try {
            $options = [
                'headers' => [
                    'Authorization' => config('fressnapf.token'),
                    'Accept'        => 'application/json',
                ],
            ];

            if (! empty($parameters)) {
                $options['json'] = $parameters;
            }

            if (! empty($query)) {
                $options['query'] = $query;
            }

            $response = $this->getClient()->{$httpMethod}($url, $options);

            return json_decode((string)$response->getBody(), true);
        } catch (RequestException $e) {
            // hier kannst du noch $e->getResponse() auslesen, wenn gewünscht
            throw $e;
        }
    }

    // ========================= base methods ======================================

    public function _get(string $url = null, array $parameters = [], array $query = []): ?array
    {
        return $this->execute('get', $url, $parameters, $query);
    }

    public function _post(string $url = null, array $parameters = [], array $query = []): ?array
    {
        return $this->execute('post', $url, $parameters, $query);
    }

    public function _put(string $url = null, array $parameters = [], array $query = []): ?array
    {
        return $this->execute('put', $url, $parameters, $query);
    }

    public function _patch(string $url = null, array $parameters = [], array $query = []): ?array
    {
        return $this->execute('patch', $url, $parameters, $query);
    }

    public function _delete(string $url = null, array $parameters = [], array $query = []): ?array
    {
        return $this->execute('delete', $url, $parameters, $query);
    }
}
