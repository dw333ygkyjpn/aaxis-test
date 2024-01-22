<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    private static ?UserPasswordHasherInterface $hasher = null;

    protected function createAuthenticatedClient(string $username = 'aaxis', string $password = 'aaxis'): KernelBrowser
    {
        $client = static::createClient();
        $container = $client->getContainer();

        if (self::$hasher === null) {
            self::$hasher = $container->get(UserPasswordHasherInterface::class);
        }

        UserFactory::createOne([
            'username' => 'aaxis',
            'roles' => ['ROLE_ADMIN'],
            'password' => self::$hasher->hashPassword(new User(), 'aaxis'),
        ]);

        /**
         * @var string $credentials
         */
        $credentials = json_encode([
            'username' => $username,
            'password' => $password,
        ]);

        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $credentials
        );

        /**
         * @var string $response
         */
        $response = $client->getResponse()->getContent();

        /**
         * @var array<string> $data
         */
        $data = json_decode($response, true);
        $token = $data['token'];

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        return $client;
    }

    public function testGetProduct(): void
    {
        $client = $this->createAuthenticatedClient();

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
        $client = $this->createAuthenticatedClient();
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
        $client = $this->createAuthenticatedClient();
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
        $client = $this->createAuthenticatedClient();
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
        $client = $this->createAuthenticatedClient();
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
