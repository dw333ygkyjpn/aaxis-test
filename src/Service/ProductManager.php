<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorService $validatorService,
        private NormalizerInterface $normalizer,
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function processProduct(Product $product): array
    {
        $productValidation = $this->validatorService->validate($product);

        if (!empty($productValidation)) {
            return ResponseFormatter::formatUnprocessableEntity($product->getSku(), $productValidation);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return ResponseFormatter::formatCreated($product->getSku(), $this->normalizer->normalize($product));
    }

    /**
     * @return array<string, mixed>
     */
    public function updateProduct(ProductDTO $productDTO, string $method = 'PUT'): array
    {
        $existingProduct = $this->productRepository->findOneBy(['sku' => $productDTO->getSku()]);

        if (empty($existingProduct)) {
            return ResponseFormatter::formatNotFound($productDTO->getSku());
        }

        if ($method == 'PATCH') {
            $existingProduct->patchFromDTO($productDTO);
        } else {
            $existingProduct->updateFromDTO($productDTO);
        }

        $this->entityManager->flush();

        return ResponseFormatter::formatUpdated($existingProduct->getSku(), $this->normalizer->normalize($existingProduct));
    }
}
