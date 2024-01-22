<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $message = $exception->getMessage();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = $exception->getHeaders();

            $data = [
                'title' => 'HTTP Status: '.$statusCode,
                'status' => $statusCode,
                'detail' => $message,
            ];

            $previousException = $exception->getPrevious();

            if ($previousException instanceof ValidationFailedException) {
                $data['title'] = ' Unprocessable entity';
                $violations = $previousException->getViolations();
                $violationArray = [];

                foreach ($violations as $violation) {
                    $violationArray[] = [
                        'path' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                        'invalid_value' => $violation->getInvalidValue(),
                    ];
                }

                $data['violations'] = $violationArray;
            }

            $response = new JsonResponse($data, $statusCode, $headers);
            $event->setResponse($response);
        }
    }
}
