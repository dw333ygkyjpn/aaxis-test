<?php

declare(strict_types=1);

namespace App\Tests\DTO;

use App\DTO\ProductDTO;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductDTOTest extends TestCase
{
    public function testGenerateEntity(): void
    {
        $productDTO = new ProductDTO();
        $productDTO->setSku('sku-test');
        $productDTO->setName('Test Product');
        $productDTO->setDescription('Product for testing');

        $generatedEntity = $productDTO->generateEntity();

        $this->assertInstanceOf(Product::class, $generatedEntity);
        $this->assertSame('sku-test', $generatedEntity->getSku());
        $this->assertSame('Test Product', $generatedEntity->getName());
        $this->assertSame('Product for testing', $generatedEntity->getDescription());
    }

    public function testGenerateEntityWithNullData(): void
    {
        $productDTO = new ProductDTO();
        $this->expectException(\RuntimeException::class);
        $productDTO->generateEntity();
    }
}
