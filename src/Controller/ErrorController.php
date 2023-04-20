<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorController
{

    public function showError($exception, LoggerInterface $logger): JsonResponse
    {
        $logger->error($exception->getMessage());

        return new JsonResponse([
            'exception' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getStatusCode(),
            ],
        ], $exception->getStatusCode());
    }
}
