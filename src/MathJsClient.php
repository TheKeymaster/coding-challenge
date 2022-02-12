<?php

namespace Thekeymaster\CodingChallenge;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

class MathJsClient
{
    private const BASE_URI = 'https://api.mathjs.org/v4/';

    public function __construct(
        private ClientInterface $client = new Client(['base_uri' => self::BASE_URI])
    ) {}

    public function sendExpression(array $mathExpressions): string
    {
        $response = $this->client->request('POST', '', [
                'json' => ['expr' => $mathExpressions]
            ]
        );

        return $response->getBody()->__toString();
    }
}
