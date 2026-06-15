<?php

declare(strict_types=1);
/**
 * This file is part of gokure/hyperf-http-logger.
 *
 * @link     https://github.com/gokure/hyperf-http-logger
 * @document https://github.com/gokure/hyperf-http-logger/blob/main/README.md
 * @contact  gokure@gmail.com
 * @license  https://github.com/hyperf/hyperf-http-logger/blob/main/LICENSE
 */

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
