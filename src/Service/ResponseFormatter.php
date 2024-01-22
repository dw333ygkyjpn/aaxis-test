<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class ResponseFormatter
{
    /**
     * @return array<string, mixed>
     */
    public static function formatCreated(null|string $id, mixed $data): array
    {
        return [
            'id' => $id,
            'title' => 'Resource created!',
            'status' => Response::HTTP_OK,
            'data' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function formatUpdated(null|string $id, mixed $data): array
    {
        return [
            'id' => $id,
            'title' => 'Resource updated!',
            'status' => Response::HTTP_OK,
            'data' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function formatOk(null|string $id, mixed $data): array
    {
        return [
            'id' => $id,
            'title' => 'OK!',
            'status' => Response::HTTP_OK,
            'data' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function formatOkCollection(mixed $data): array
    {
        return [
            'title' => 'OK!',
            'status' => Response::HTTP_OK,
            'data' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function formatNotFound(null|string $id): array
    {
        return [
            'id' => $id,
            'title' => 'Resource not found',
            'status' => Response::HTTP_NOT_FOUND,
        ];
    }

    /**
     * @param array<mixed> $violations
     *
     * @return array<string, mixed>
     */
    public static function formatUnprocessableEntity(null|string $id, array $violations): array
    {
        return [
            'id' => $id,
            'title' => 'Unprocessable resource',
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'violations' => $violations,
        ];
    }
}
