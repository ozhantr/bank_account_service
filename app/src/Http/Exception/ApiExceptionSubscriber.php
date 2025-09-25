<?php

namespace App\Http\Exception;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        // DTO Validation
        if ($e instanceof ValidationFailedException) {
            $errors = [];
            foreach ($e->getViolations() as $v) {
                $errors[] = ['field' => $v->getPropertyPath(), 'message' => $v->getMessage()];
            }
            $event->setResponse(new JsonResponse(['errors' => $errors], 400));

            return;
        }

        // HttpException
        if ($e instanceof HttpExceptionInterface) {
            $event->setResponse(new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode()));

            return;
        }

        if ($e instanceof AccessDeniedHttpException) {
            $event->setResponse(new JsonResponse(['error' => 'Forbidden'], 403));

            return;
        }

        if ($e instanceof BadRequestHttpException) {
            $event->setResponse(new JsonResponse(['error' => 'Bad request'], 400));

            return;
        }

        $event->setResponse(new JsonResponse(['error' => 'Internal Server Error'], 500));
    }
}
