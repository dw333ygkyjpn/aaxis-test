<?php

namespace App\Service\Serializer;

use App\DTO\ProductDTO;
use App\Entity\Product;
use Symfony\Component\Serializer\SerializerInterface;

class ProductSerializer
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function deserialize(ProductDTO $productDto): Product
    {
        return $this->serializer->denormalize($productDto, Product::class);
    }

    public function serialize(Product $product): string
    {
        return $this->serializer->serialize($product, 'json');
    }
}