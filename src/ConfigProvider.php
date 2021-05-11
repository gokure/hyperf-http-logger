<?php

declare(strict_types=1);

namespace Gokure\HttpLogger;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for http logger.',
                    'source' => __DIR__ . '/../publish/http_logger.php',
                    'destination' => BASE_PATH . '/config/autoload/http_logger.php',
                ],
            ],
        ];
    }
}
