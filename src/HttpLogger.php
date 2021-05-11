<?php

declare(strict_types=1);

namespace Gokure\HttpLogger;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpLogger
{
    protected const HYPERF_START = __CLASS__ . '.HYPERF_START';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $options;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $options = $this->container->get(ConfigInterface::class)->get('http_logger', []);
        $this->options = $this->normalizeOptions($options);
    }

    public function setUp(): void
    {
        Context::set(static::HYPERF_START, microtime(true));
    }

    public function record(ResponseInterface $response, ServerRequestInterface $request, $level = Logger::INFO): void
    {
        if (!$this->shouldRecord($response, $request)) {
            return;
        }

        $context = [];

        if ($this->shouldRecordContext($response, $request)) {
            $context['request'] = array_filter([
                'body' => $request->getParsedBody(),
                'files' => collect($request->getUploadedFiles())
                    ->flatten()
                    ->map(static function ($file) {
                        return [
                            'name' => $file->getClientFilename(),
                            'size' => $file->getSize(),
                            'type' => $file->getClientMediaType(),
                        ];
                    })->toArray(),
            ]);

            $context['response'] = array_filter([
                'body' => (string)$response->getBody(),
            ]);
        }

        $requestTime = microtime(true) - Context::get(static::HYPERF_START, microtime(true));
        Context::destroy(static::HYPERF_START);

        // "GET /path HTTP/1.1" 200 0.026 "User-Agent"
        $message = sprintf('"%s %s HTTP/%s" %s %s "%s"',
            $request->getMethod(),
            $request->getRequestTarget(),
            $request->getProtocolVersion(),
            $response->getStatusCode(),
            number_format($requestTime, 3),
            $request->getHeaderLine('User-Agent')
        );

        if ($this->options['long_request_time'] > 0 && $requestTime >= $this->options['long_request_time']) {
            $logger = $this->container->get(LoggerFactory::class)->get('http-slow');
        } else {
            $logger = $this->container->get(LoggerFactory::class)->get('http');
        }

        $logger->log($level, $message, $context);
    }

    protected function shouldRecord(ResponseInterface $response, ServerRequestInterface $request): bool
    {
        return !$this->isSuccessful($response) ||
            $this->options['allowed_methods'] === true ||
            in_array(strtoupper($request->getMethod()), $this->options['allowed_methods'], true);
    }

    protected function shouldRecordContext(ResponseInterface $response, ServerRequestInterface $request): bool
    {
        return !$this->isSuccessful($response) ||
            $this->options['allowed_context_methods'] === true ||
            in_array(strtoupper($request->getMethod()), $this->options['allowed_context_methods'], true);
    }

    protected function isSuccessful(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    protected function normalizeOptions(array $options = []): array
    {
        $options += [
            'allowed_methods' => ['*'],
            'allowed_context_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],
            'slow_request_log' => false,
            'long_request_time' => 3.0,
            'logger' => [
                'handler' => [
                    'class' => \Monolog\Handler\StreamHandler::class,
                    'constructor' => [
                        'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
                    ],
                ],
                'formatter' => [
                    'class' => \Monolog\Formatter\LineFormatter::class,
                    'constructor' => [
                        'format' => null,
                        'dateFormat' => 'Y-m-d H:i:s',
                        'allowInlineLineBreaks' => false,
                    ],
                ],
            ],
        ];

        foreach (['allowed_methods', 'allowed_context_methods'] as $key) {
            if (!is_array($options[$key])) {
                throw new \RuntimeException('Http Logger config `' . $key . '` should be an array.');
            }
        }

        if (in_array('*', $options['allowed_methods'], true)) {
            $options['allowed_methods'] = true;
        } else {
            $options['allowed_methods'] = array_map('strtoupper', $options['allowed_methods']);
        }

        if (in_array('*', $options['allowed_context_methods'], true)) {
            $options['allowed_context_methods'] = true;
        } else {
            $options['allowed_context_methods'] = array_map('strtoupper', $options['allowed_context_methods']);
        }

        return $options;
    }
}
