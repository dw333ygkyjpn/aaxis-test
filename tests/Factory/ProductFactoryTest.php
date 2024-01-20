<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Product;
use App\Factory\ProductFactory;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class ProductFactoryTest extends TestCase
{
    use Factories;

    /**
     * @dataProvider productFactoryDataProvider
     */
    public function testProductFactory(ProductFactory $factory): void
    {
        $product = $factory->create()->object();
        $this->assertInstanceOf(Product::class, $product);
    }

    /**
     * @return iterable<mixed>
     */
    public static function productFactoryDataProvider(): iterable
    {
        yield [ProductFactory::new()->withoutPersisting()];
    }
}
