<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ValidatorService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorServiceTest extends TestCase
{
    private ValidatorInterface&MockObject $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    public function testValidate(): void
    {
        $dummyObject = new \stdClass();

        $violation1 = new ConstraintViolation('Message 1', null, [], $dummyObject, 'propertyName1', 'invalidValue1');
        $violation2 = new ConstraintViolation('Message 2', null, [], $dummyObject, 'propertyName2', 'invalidValue2');
        $violationList = new ConstraintViolationList([$violation1, $violation2]);

        // Mock the behavior of the ValidatorInterface
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($dummyObject, null, 'Default')
            ->willReturn($violationList);

        $validatorService = new ValidatorService($this->validator);
        $result = $validatorService->validate($dummyObject);

        // Define the expected result based on the violations
        $expectedResult = [
            [
                'path' => 'propertyName1',
                'message' => 'Message 1',
                'invalid_value' => 'invalidValue1',
            ],
            [
                'path' => 'propertyName2',
                'message' => 'Message 2',
                'invalid_value' => 'invalidValue2',
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
