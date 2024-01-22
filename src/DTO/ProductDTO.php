<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    #[Assert\NotBlank(groups: ['Default', 'patch'])]
    private string|null $sku = null;

    #[Assert\NotBlank(groups: ['Default'])]
    private string|null $name = null;

    private string|null $description = null;

    public function getSku(): string|null
    {
        return $this->sku;
    }

    public function setSku(string|null $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string|null $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function setDescription(string|null $description): void
    {
        $this->description = $description;
    }

    public function generateEntity(): Product
    {
        if (empty($this->getName()) || empty($this->getSku())) {
            throw new \RuntimeException('Cannot create entity with null data.');
        }

        $product = new Product();
        $product->setSku($this->getSku())
            ->setName($this->getName())
            ->setDescription($this->getDescription())
        ;

        return $product;
    }
}
