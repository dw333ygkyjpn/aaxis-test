<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/product', name: 'product_')]
class ProductController extends AbstractController
{
    /**
     * Uses EntityValueResolver to automatically query for the product object :).
     *
     * @see https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-entityvalueresolver
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);
    }
}
