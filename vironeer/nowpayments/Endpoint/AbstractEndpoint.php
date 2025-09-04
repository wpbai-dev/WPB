<?php

namespace Vironeer\NOWPayments\Endpoint;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Vironeer\NOWPayments\Client;

abstract class AbstractEndpoint implements EndpointInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function getUrl(string $endpoint = null): string
    {
        return implode('/', [
            $this->client->getApiEndpoint(),
            $this->client::API_VERSION,
            $this->getResource(),
            $endpoint,
        ]);
    }

    protected function request(string $method = self::METHOD_GET, string $endpoint = null, array $data = [], array $query = []): array
    {
        $options = [
            RequestOptions::HEADERS => [
                'x-api-key' => $this->client->getApiKey(),
            ],
            RequestOptions::QUERY => $query,
        ];

        if ($method == self::METHOD_POST) {
            $options[RequestOptions::JSON] = $data;
        }

        try {

            $guzzleClient = new GuzzleClient();
            $response = $guzzleClient->request($method, $this->getUrl($endpoint), $options);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $requestException) {

            if ($requestException->hasResponse()) {

                return json_decode($requestException->getResponse()->getBody()->getContents(), true);

            }

            return [
                'message' => 'no response',
            ];
        }
    }
}
