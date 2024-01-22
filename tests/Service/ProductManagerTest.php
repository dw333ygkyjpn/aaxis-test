<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductManager;
use App\Service\ResponseFormatter;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class ProductManagerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private ValidatorService&MockObject $validatorService;
    private Serializer&MockObject $serializer;
    private ProductRepository&MockObject $productRepository;

    protected function setUp(): void
    {
        // You may need to mock or use a real instance of these dependencies.
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->validatorService = $this->createMock(ValidatorService::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
    }

    public function testProcessProduct(): void
    {
        $product = new Product();
        $product->setSku('sku-test')
            ->setName('test name')
            ->setDescription('test desc')
        ;

        // Mocking the behavior of the ValidatorService
        $this->validatorService
            ->expects($this->once())
            ->method('validate')
            ->willReturn([]);

        // Mocking the behavior of the EntityManager
        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Mocking the behavior of the Serializer
        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->willReturn(['normalized' => 'data']);

        $productManager = new ProductManager(
            $this->entityManager,
            $this->validatorService,
            $this->serializer,
            $this->productRepository
        );

        $response = $productManager->processProduct($product);

        $this->assertEquals(
            ResponseFormatter::formatCreated('sku-test', ['normalized' => 'data']),
            $response
        );
    }

    public function testUpdateProduct(): void
    {
        $productDTO = new ProductDTO();
        $productDTO->setSku('sku-test');
        $productDTO->setName('changed product name');
        $productDTO->setDescription('changed product desc');

        $existingProduct = new Product();
        $existingProduct->setSku('sku-test')
            ->setName('test name')
            ->setDescription('test desc')
        ;

        $this->productRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['sku' => 'sku-test'])
            ->willReturn($existingProduct);

        // Mocking the behavior of the ValidatorService
        $this->validatorService
            ->expects($this->never())
            ->method('validate')
            ->willReturn([]);

        // Mocking the behavior of the EntityManager
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Mocking the behavior of the Serializer
        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->willReturn(['normalized' => 'data']);

        $productManager = new ProductManager(
            $this->entityManager,
            $this->validatorService,
            $this->serializer,
            $this->productRepository
        );

        $response = $productManager->updateProduct($productDTO);

        $this->assertEquals(
            ResponseFormatter::formatUpdated('sku-test', ['normalized' => 'data']),
            $response
        );
    }
}
