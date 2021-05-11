<?php

declare(strict_types=1);

namespace Gokure\HttpLogger;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpLoggerMiddleware implements MiddlewareInterface
{
    /**
     * @var HttpLogger
     */
    protected $httpLogger;

    public function __construct(HttpLogger $httpLogger)
    {
        $this->httpLogger = $httpLogger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->httpLogger->setUp();
        $response = $handler->handle($request);
        $this->httpLogger->record($response, $request);

        return $response;
    }
}
