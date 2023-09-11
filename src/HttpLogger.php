<?php

declare(strict_types=1);

namespace Gokure\HttpLogger;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Logger\LoggerFactory;
use Monolog\Level;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use function \Hyperf\Collection\collect;

class HttpLogger
{
    protected LoggerInterface $logger;

    protected array $options;

    public function __construct(LoggerFactory $factory, ConfigInterface $config)
    {
        $this->options = $this->normalizeOptions($config->get('http_logger', []));
        $name = $this->options['logger']['name'] ?? 'hyperf';
        $group = $this->options['logger']['group'] ?? 'default';
        $this->logger = $factory->get($name, $group);
    }

    public function record(\Throwable|ResponseInterface $response, ServerRequestInterface $request): void
    {
        if (! $this->shouldRecord($response, $request)) {
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

            if ($response instanceof \Throwable) {
                $context['exception'] = [
                    'message' => $response->getMessage(),
                    'trace' => $response->getTraceAsString(),
                ];
            } else {
                $context['response'] = array_filter([
                    'body' => (string)$response->getBody(),
                ]);
            }
        }

        $server = $request->getServerParams();
        if (isset($server['request_time_float'])) {
            $startTime = $server['request_time_float'];
            $executeTime = number_format(microtime(true) - $startTime, 3);
        } else {
            $executeTime = '-';
        }

        // "GET /path HTTP/1.1" 200 0.026 "User-Agent"
        $message = sprintf('"%s %s HTTP/%s" %s %s "%s"',
            $request->getMethod(),
            $request->getRequestTarget(),
            $request->getProtocolVersion(),
            $response instanceof ResponseInterface ? $response->getStatusCode() : '-',
            $executeTime,
            $request->getHeaderLine('user-agent')
        );

        $level = $response instanceof \Throwable ? Level::Error : Level::Info;
        $this->logger->log($level, $message, $context);
    }

    protected function shouldRecord(\Throwable|ResponseInterface $response, ServerRequestInterface $request): bool
    {
        return $response instanceof \Throwable ||
            ! $this->isSuccessful($response) ||
            (
                (
                    $this->options['allowed_methods'] === true ||
                    in_array(strtoupper($request->getMethod()), $this->options['allowed_methods'], true)
                ) && $this->options['bypass_function']($response, $request) !== true
            );
    }

    protected function shouldRecordContext(\Throwable|ResponseInterface $response, ServerRequestInterface $request): bool
    {
        return $response instanceof \Throwable ||
            ! $this->isSuccessful($response) ||
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
            'logger' => [
                'name' => 'hyperf',
                'group' => 'default',
            ],
        ];

        if (! isset($options['bypass_function'])) {
            $options['bypass_function'] = function () {};
        }

        foreach (['allowed_methods', 'allowed_context_methods'] as $key) {
            if (! is_array($options[$key])) {
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
