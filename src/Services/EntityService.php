<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EntityService
{
    private string $baseUrl = 'https://jsonplaceholder.typicode.com';

    public function __construct(public HttpClientInterface $client)
    {
    }

    /**
     * @param string $path
     * @param string $query
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ExceptionInterface
     * @return array
     */
    public function getData(string $path, string $query = ''): array
    {
        return $this->client->request(
            'GET',
            "$this->baseUrl/$path?$query",
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        )->toArray();
    }
}
