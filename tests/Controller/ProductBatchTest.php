<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductBatchTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    private static ?UserPasswordHasherInterface $hasher = null;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->createDefaultUser();
    }

    private function createDefaultUser(): void
    {
        $container = $this->client->getContainer();

        if (self::$hasher === null) {
            self::$hasher = $container->get(UserPasswordHasherInterface::class);
        }

        UserFactory::createOne([
            'username' => 'aaxis',
            'roles' => ['ROLE_ADMIN'],
            'password' => self::$hasher->hashPassword(new User(), 'aaxis'),
        ]);
    }

    protected function createAuthenticatedClient(string $username = 'aaxis', string $password = 'aaxis'): void
    {
        /**
         * @var string $credentials
         */
        $credentials = json_encode([
            'username' => $username,
            'password' => $password,
        ]);

        $this->client->request(
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
        $response = $this->client->getResponse()->getContent();

        /**
         * @var array<string> $data
         */
        $data = json_decode($response, true);
        $token = $data['token'];

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }

    /**
     * @dataProvider provideBatchRequest
     *
     * @param array<mixed> $payload
     */
    public function testBatch(string $method, int $expectedCode, array $payload): void
    {
        $this->createAuthenticatedClient();
        $router = $this->client->getContainer()->get('router');

        ProductFactory::createOne([
            'sku' => 'sku-test-1',
            'name' => 'test',
            'description' => 'test description',
            'createdAt' => new \DateTimeImmutable(),
            'updatedAt' => new \DateTimeImmutable(),
        ]);

        ProductFactory::createOne([
            'sku' => 'sku-test-2',
            'name' => 'test',
            'description' => 'test description',
            'createdAt' => new \DateTimeImmutable(),
            'updatedAt' => new \DateTimeImmutable(),
        ]);

        /**
         * @var string $payload
         */
        $payload = json_encode($payload);

        $this->client->request(
            $method,
            $router->generate('product_batch_job'),
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
    public static function provideBatchRequest(): iterable
    {
        $payloadPost = [
            [
                'sku' => 'sku-test-4',
                'name' => 'sku-test-4',
                'description' => 'description 4',
            ],
            [
                'sku' => 'sku-test-5',
                'name' => 'sku-test-5',
                'description' => 'updated name 5',
            ],
        ];
        yield 'post' => [Request::METHOD_POST, Response::HTTP_OK, $payloadPost];

        $payloadPostFailed = [
            [
                'sku' => 'sku-test-4',
                'name' => 'sku-test-4',
                'description' => 'description 4',
            ],
            [
                'sku' => 'sku-test-5',
                'description' => 'fail no name sent',
            ],
        ];
        yield 'failed-post' => [Request::METHOD_POST, Response::HTTP_MULTI_STATUS, $payloadPostFailed];

        $payloadPostFailed2 = [
            [
                'sku' => 'sku-test-4',
                'name' => 'sku-test-4',
                'description' => 'description 4',
            ],
            [
                'sku' => 'sku-test-1',
                'name' => 'sku-test-1',
                'description' => 'fail unique constraint',
            ],
        ];
        yield 'failed-post-2' => [Request::METHOD_POST, Response::HTTP_MULTI_STATUS, $payloadPostFailed2];

        $payloadPatch = [
            [
                'sku' => 'sku-test-1',
                'description' => 'updated description 1',
            ],
            [
                'sku' => 'sku-test-2',
                'name' => 'updated name 2',
            ],
        ];
        yield 'patch' => [Request::METHOD_PATCH, Response::HTTP_OK, $payloadPatch];

        $payloadPut = [
            [
                'sku' => 'sku-test-1',
                'name' => 'updated name 1',
                'description' => 'updated description 1',
            ],
            [
                'sku' => 'sku-test-2',
                'name' => 'updated name 2',
                'description' => 'updated description 2',
            ],
        ];
        yield 'put' => [Request::METHOD_PUT, Response::HTTP_OK, $payloadPut];

        $payloadEmpty = [];
        yield 'empty-post' => [Request::METHOD_POST, Response::HTTP_BAD_REQUEST, $payloadEmpty];

        $payloadNotArray = [
            'string' => 'string',
        ];
        yield 'not-array-post' => [Request::METHOD_POST, Response::HTTP_BAD_REQUEST, $payloadNotArray];

        $payloadFailPut = [
            [
                'sku' => 'sku-test-9',
                'name' => 'this product does not exist',
                'description' => 'this should fail',
            ],
        ];
        yield 'fail-put' => [Request::METHOD_PUT, Response::HTTP_MULTI_STATUS, $payloadFailPut];
    }
}
