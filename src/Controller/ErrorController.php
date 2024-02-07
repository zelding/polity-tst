<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ErrorController extends AbstractController
{
    public function renderJson(Request $request, Throwable $exception, ?LoggerInterface $logger = null): JsonResponse
    {
        $code    = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = "Internal error";

        if ( $exception instanceof HttpExceptionInterface) {
            $code    = $exception->getStatusCode();
            $message = $exception->getMessage();
        }

        return $this->json([
            "message" => $message,
            "code"    => $code
        ], $code);
    }
}