<?php

declare(strict_types=1);

namespace Gokure\HttpLogger;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpLoggerExceptionHandler extends ExceptionHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if (class_exists(HttpLogger::class) && $this->container->has(HttpLogger::class)) {
            $request = $this->container->get(RequestInterface::class);
            $this->container->get(HttpLogger::class)->record($response, $request, Logger::ERROR);
        }

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
