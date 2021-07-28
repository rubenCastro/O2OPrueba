<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Exception\ApiException;

class KernelExceptionListener
{
    private $devMode;

    public function __construct(string $environment)
    {
        $this->devMode = $environment === 'dev';
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof ApiException) {
            $message = $throwable->getPublicMessage();
            $status = $throwable->getHttpStatus();
        } else {
            $message = $throwable->getMessage();
            $status = 500;
        }

        $responseArr = [
            'message' => $message,
            'status' => $status,
        ];

        if ($this->devMode) {
            $responseArr['file'] = $throwable->getFile();
            $responseArr['line'] = $throwable->getLine();
            $responseArr['trace'] = $throwable->getTrace();
        }

        $response = new JsonResponse($responseArr, $status);

        $event->setResponse($response);
    }
}