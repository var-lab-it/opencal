<?php

declare(strict_types=1);

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpFoundation\Response;

class DayAvailabilityTest extends ApiTestCase
{
    use MatchesSnapshots;

    #[DataProvider('availabilityFiltersDataProvider')]
    public function testAvailabilityFilters(string $url, int $expectedStatusCode): void
    {
        $client = static::createClient();

        $response = $client->request('GET', $url);
        self::assertSame($expectedStatusCode, $response->getStatusCode());
    }

    /** @return array<array-key, array<int, string|int>> */
    public static function availabilityFiltersDataProvider(): array
    {
        return [
            ['/availability/day', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['/availability/day?email=test%40mail.com', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['/availability/day?date=2021-01-01', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['/availability/day?date=2021-01-01&email=test%40mail.com&event_type_id=1', Response::HTTP_NOT_FOUND],
            ['/availability/day?date=2021-01-01&email=user%40example.tld&event_type_id=1', Response::HTTP_OK],
        ];
    }

    #[DataProvider('availabilityDaysDataProvider')]
    public function testAvailabilityDays(string $url, int $expectedStatus): void
    {
        $client = static::createClient();

        $response = $client->request('GET', $url, [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
        self::assertSame(
            $expectedStatus,
            $response->getStatusCode(),
        );

        if (Response::HTTP_OK !== $expectedStatus) {
            return;
        }

        $json = $response->toArray();

        self::assertMatchesJsonSnapshot($json);
    }

    /** @return array<array-key, array<int, string|int>> */
    public static function availabilityDaysDataProvider(): array
    {
        return [
            [
                '/availability/day?email=user%40example.tld&date=2025-05-05&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability/day?email=user%40example.tld&date=2025-05-06&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability/day?email=user%40example.tld&date=2025-05-07&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability/day?email=user%40example.tld&date=2025-05-08&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability/day?email=user%40example.tld&date=2025-05-09&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability/day?email=user%40example.tld&date=2025-05-10&event_type_id=1',
                Response::HTTP_OK,
            ],
            [
                '/availability/day?email=user%40example.tld&date=2025-05-11&event_type_id=1',
                Response::HTTP_OK,
            ],
        ];
    }
}
