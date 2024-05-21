<?php

declare(strict_types=1);

namespace Application;

use Domain\Entities\Transaction;
use Domain\Exceptions\NotificationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

use function array_rand;
use function json_decode;

class NotificationService
{
    /** @var string[] $urls */
    private array $urls = [
        'https://run.mocky.io/v3/90e6cd9f-f29b-44e6-ba98-248a2f1f9d58', // notificação enviada
//        'https://run.mocky.io/v3/35e0a34f-cbab-4994-a9b6-48c1af862750', // notificação não enviada
    ];

    public function __construct(
        private readonly Client $client
    ) {
    }

    public function notify(Transaction $transaction): void
    {
        try {
            $url = $this->urls[array_rand($this->urls)];

            $response = $this->client->post($url, [
                'json' => [
                    'payee' => $transaction->getPayeeId(),
                    'value' => $transaction->getValue(),
                ],
            ]);

            /** @var array{success: bool, type: string, message: string} $data */
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException) {
            throw NotificationException::forErrorDuringConnection();
        }

        if ($data['success'] === false) {
            throw NotificationException::forErrorDuringNotification();
        }
    }
}
