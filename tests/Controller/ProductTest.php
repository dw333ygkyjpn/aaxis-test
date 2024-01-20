<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testGetProduct(): void
    {
        $client = static::createClient();
        $product = ProductFactory::createOne();
        $router = $client->getContainer()->get('router');
        $crawler = $client->request('GET',
            $router->generate(
                'product_get',
                ['id' => 1]
            )
        );

        $this->assertResponseIsSuccessful();
        $this->isJson();
    }
}
