<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @return array<mixed>
     */
    public function validate(object $object, string $group = 'Default'): array
    {
        $violations = $this->validator->validate($object, null, $group);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'path' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'invalid_value' => $violation->getInvalidValue(),
            ];
        }

        return $errors;
    }
}
