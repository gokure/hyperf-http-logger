<?php

declare(strict_types=1);

namespace Gokure\HttpLogger\Processor;

use Hyperf\Utils\ApplicationContext;
use Monolog\Processor\ProcessorInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * Injects value of request headers in all records
 */
class RequestHeadersProcessor implements ProcessorInterface
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
        /** @var RequestInterface $request */
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);

        foreach ($this->headers as $header) {
            if ($request->hasHeader($header)) {
                $record['extra']['req_' . strtolower($header)] = $request->getHeaderLine($header);
            }
        }

        return $record;
    }
}
