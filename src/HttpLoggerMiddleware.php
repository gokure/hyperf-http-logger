<?php

declare(strict_types=1);

namespace Gokure\HttpLogger;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpLoggerMiddleware implements MiddlewareInterface
{
    public function __construct(protected HttpLogger $logger)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
            $this->logger->record($response, $request);
            return $response;
        } catch (\Throwable $e) {
            $this->logger->record($e, $request);
            throw $e;
        }
    }
}
