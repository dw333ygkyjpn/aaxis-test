<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\ModelFactory;

final class ProductFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'sku' => self::faker()->unique()->regexify('[A-Z]{5}[0-4]{3}'),
            'name' => self::faker()->word(),
            'description' => self::faker()->optional()->sentence(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Product::class;
    }
}
