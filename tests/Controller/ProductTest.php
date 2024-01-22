<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testGetProduct(): void
    {
        $client = static::createClient();
        $product = ProductFactory::createOne([
            'sku' => 'sku-test',
            'name' => 'test',
            'description' => 'test description',
            'createdAt' => new \DateTimeImmutable(),
            'updatedAt' => new \DateTimeImmutable(),
        ]);

        $router = $client->getContainer()->get('router');
        $crawler = $client->request('GET',
            $router->generate(
                'product_get',
                ['sku' => 'sku-test']
            )
        );

        $this->assertResponseIsSuccessful();
        $this->isJson();
    }

    public function testGetCollection(): void
    {
        $client = static::createClient();
        ProductFactory::createMany(10);

        $router = $client->getContainer()->get('router');
        $crawler = $client->request('GET', $router->generate('product_get_collection'));

        $this->assertResponseIsSuccessful();
        $this->isJson();
    }

    /**
     * @dataProvider provideRequest
     *
     * @param array<mixed> $payload
     */
    public function testPost(int $expectedCode, array $payload): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');

        /**
         * @var string $payload
         */
        $payload = json_encode($payload);

        $client->request(
            'POST',
            $router->generate('product_post'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $this->assertResponseStatusCodeSame($expectedCode);
    }

    /**
     * @dataProvider provideRequest
     *
     * @param array<mixed> $payload
     */
    public function testPut(int $expectedCode, array $payload): void
    {
        $client = static::createClient();
        ProductFactory::createOne([
            'sku' => 'sku-test',
            'name' => 'test',
            'description' => 'test description',
            'createdAt' => new \DateTimeImmutable(),
            'updatedAt' => new \DateTimeImmutable(),
        ]);

        $router = $client->getContainer()->get('router');

        /**
         * @var string $payload
         */
        $payload = json_encode($payload);

        $client->request(
            'PUT',
            $router->generate('product_update'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $this->assertResponseStatusCodeSame($expectedCode);
    }

    /**
     * @dataProvider providePatchRequest
     *
     * @param array<mixed> $payload
     */
    public function testPatch(int $expectedCode, array $payload): void
    {
        $client = static::createClient();
        ProductFactory::createOne([
            'sku' => 'sku-test',
            'name' => 'test',
            'description' => 'test description',
            'createdAt' => new \DateTimeImmutable(),
            'updatedAt' => new \DateTimeImmutable(),
        ]);

        $router = $client->getContainer()->get('router');

        /**
         * @var string $payload
         */
        $payload = json_encode($payload);

        $client->request(
            'PATCH',
            $router->generate('product_patch'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $this->assertResponseStatusCodeSame($expectedCode);
    }

    /**
     * @return iterable<mixed>
     */
    public static function provideRequest(): iterable
    {
        $payload = [
            'sku' => 'sku-test',
            'name' => 'product test',
            'description' => 'product for testing',
        ];
        yield 'ok' => [Response::HTTP_OK, $payload];

        $payload = [
            'sku' => 'sku-test',
            'description' => 'fail no name',
        ];
        yield 'no-name' => [Response::HTTP_UNPROCESSABLE_ENTITY, $payload];

        $payload = [
            'name' => 'fail-sku',
            'description' => 'fail no sku',
        ];
        yield 'no-sku' => [Response::HTTP_UNPROCESSABLE_ENTITY, $payload];
    }

    /**
     * @return iterable<mixed>
     */
    public static function providePatchRequest(): iterable
    {
        $payload = [
            'sku' => 'sku-test',
            'description' => 'product for testing',
        ];
        yield 'ok' => [Response::HTTP_OK, $payload];

        $payload = [
            'sku' => 'sku-test',
            'name' => 'changed name',
        ];
        yield 'ok-2' => [Response::HTTP_OK, $payload];

        $payload = [
            'name' => 'fail-sku',
            'description' => 'fail no sku',
        ];
        yield 'no-sku' => [Response::HTTP_UNPROCESSABLE_ENTITY, $payload];
    }
}
