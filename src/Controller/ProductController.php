<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductManager;
use App\Service\ResponseFormatter;
use App\Service\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/products', name: 'product_')]
class ProductController extends AbstractController
{
    public const JSON_FORMAT = 'json';

    public function __construct(private readonly SerializerInterface $serializer,
        private readonly ProductManager $productManager)
    {
    }

    #[Route('', name: 'get_collection', methods: ['GET'])]
    public function getProductCollection(Request $request, ProductRepository $productRepository): JsonResponse
    {
        // TODO: Add search/filter query, pagination
        $products = $productRepository->findByQuery($request->getQueryString());

        return $this->json(ResponseFormatter::formatOkCollection($products));
    }

    /**
     * Uses EntityValueResolver to automatically query for the product object :).
     *
     * @see https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-entityvalueresolver
     */
    #[Route('/{sku}', name: 'get', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json(ResponseFormatter::formatOk($product->getSku(), $product));
    }

    /**
     * Uses MapRequestPayload to automatically populate the DTO with the given data.
     */
    #[Route('', name: 'post', methods: ['POST'])]
    public function postProduct(
        #[MapRequestPayload] ProductDTO $productDTO
    ): JsonResponse {
        $product = $this->productManager->processProduct($productDTO->generateEntity());

        return $this->json($product);
    }

    #[Route('', name: 'update', methods: ['PUT'])]
    public function updateProduct(
        Request $request,
        #[MapRequestPayload] ProductDTO $productDTO
    ): JsonResponse {
        $product = $this->productManager->updateProduct($productDTO, $request->getMethod());

        return $this->json($product);
    }

    #[Route('', name: 'patch', methods: ['PATCH'])]
    public function patchProduct(
        Request $request,
        #[MapRequestPayload(validationGroups: 'patch')] ProductDTO $productDTO
    ): JsonResponse {
        $product = $this->productManager->updateProduct($productDTO, $request->getMethod());

        return $this->json($product);
    }

    #[Route('/batch', name: 'batch_job', methods: ['POST', 'PUT', 'PATCH'])]
    public function batchPostProducts(Request $request,
        DenormalizerInterface $denormalizer,
        ValidatorService $validatorService): JsonResponse
    {
        $payload = $request->getContent();

        /**
         * @var array<object> $items
         */
        $items = json_decode($payload, true);

        if (!isset($items[0])) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'invalid JSON Payload');
        }

        $batchStatus = Response::HTTP_OK;
        $method = $request->getMethod();
        $responses = [];

        foreach ($items as $item) {
            $productDTO = $denormalizer->denormalize($item, ProductDTO::class, 'array');

            $dtoValidation = $validatorService->validate($productDTO, $method == 'PATCH' ? $method : 'Default');

            if (!empty($dtoValidation)) {
                $batchStatus = Response::HTTP_MULTI_STATUS;
                $responses[] = ResponseFormatter::formatUnprocessableEntity($productDTO->getSku(), $dtoValidation);
                continue;
            }

            if ($method === 'POST') {
                $responses[] = $response = $this->productManager->processProduct($productDTO->generateEntity());
            } else {
                $responses[] = $response = $this->productManager->updateProduct($productDTO, $method);
            }

            if ($response['status'] != Response::HTTP_OK) {
                $batchStatus = Response::HTTP_MULTI_STATUS;
            }
        }

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($responses, self::JSON_FORMAT),
            $batchStatus
        );
    }
}
