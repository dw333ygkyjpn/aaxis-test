<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\EventListener\ExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListenerTest extends TestCase
{
    public function testOnKernelExceptionHttp(): void
    {
        $exception = new HttpException(404, 'Not Found Exception');
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener = new ExceptionListener();
        $listener->onKernelException($event);

        /**
         * @var Response $response;
         */
        $response = $event->getResponse();

        /**
         * @var string $content;
         */
        $content = $response->getContent();

        /**
         * @var string $jsonDecoded;
         */
        $jsonDecoded = json_decode($content, true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            [
                'title' => 'HTTP Status: 404',
                'status' => 404,
                'detail' => 'Not Found Exception',
            ],
            $jsonDecoded
        );
    }

    public function testOnKernelExceptionValidation(): void
    {
        $violations = new ConstraintViolationList([new ConstraintViolation('Error Message', null, [], null, 'propertyName', 'invalidValue')]);
        $validationException = new ValidationFailedException('propertyName', $violations);

        $exception = new HttpException(400, 'Bad Request Exception', $validationException);
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener = new ExceptionListener();
        $listener->onKernelException($event);

        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        /**
         * @var string $content;
         */
        $content = $response->getContent();

        /**
         * @var string $jsonDecoded;
         */
        $jsonDecoded = json_decode($content, true);

        $this->assertEquals(
            [
                'title' => ' Unprocessable entity',
                'status' => 400, 'detail' => 'Bad Request Exception',
                'violations' => [['path' => 'propertyName', 'message' => 'Error Message', 'invalid_value' => 'invalidValue']],
            ],
            $jsonDecoded
        );
    }
}
