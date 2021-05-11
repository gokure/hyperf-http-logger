<?php

declare(strict_types=1);

namespace Gokure\HttpLogger\Processor;

use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\ApplicationContext;
use Monolog\Processor\ProcessorInterface;

/**
 * Injects value of request headers in all records
 */
class ResponseHeadersProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    protected $headers;

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function __invoke(array $record): array
    {
        /** @var ResponseInterface $response */
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);

        foreach ($this->headers as $header) {
            if ($response->hasHeader($header)) {
                $record['extra']['res_' . strtolower($header)] = $response->getHeaderLine($header);
            }
        }

        return $record;
    }
}
