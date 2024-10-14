<?php

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseBuilder {
    public function error(string $code, string $message, int $httpStatus): Response {
        if ($httpStatus < 400) {
            throw new InvalidArgumentException("HTTP status code must be an error code (>= 400), {$httpStatus} was provided");
        }
        return new JsonResponse([
            'code' => $code,
            'message' => $message,
        ], $httpStatus);
    }

    public function create(array $mixed): Response {
        return new JsonResponse($mixed);
    }
}