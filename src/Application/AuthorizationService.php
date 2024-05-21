<?php

declare(strict_types=1);

namespace Application;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

use function array_rand;
use function json_decode;

class AuthorizationService
{
    /** @var string[] $urls */
    private array $urls = [
        'https://run.mocky.io/v3/ebac7665-c480-4368-926b-e154c375ef27', // autorização externa aceita
//        'https://run.mocky.io/v3/d9364395-af76-43f5-b0a3-ca1003075f67', // autorização externa recusada
    ];

    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function canMakeTransfer(int $payerId, int $payeeId, float $value): bool
    {
        try {
            $url = $this->urls[array_rand($this->urls)];

            $response = $this->client->post($url, [
                'json' => [
                    'payer' => $payerId,
                    'payee' => $payeeId,
                    'value' => $value,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);

            /** @var array{success: bool, type: string, message: string} $response */
            return $response['success'];
        } catch (GuzzleException) {
            return false;
        }
    }
}
