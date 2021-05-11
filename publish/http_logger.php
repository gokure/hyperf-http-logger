<?php

return [
    /**
     * Matches the log request method. `['*']` allows all methods.
     */
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],

    /**
     * Matches the log context of request method. `['*']` allows all methods.
     */
    'allowed_context_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],

    /**
     * Sets the slow requests when > 0
     */
    'long_request_time' => (float)env('HTTP_LOGGER_LONG_REQUEST_TIME', 3.0),

    /**
     * Sets the logger instance, same as the logger.php file in hyperf.
     */
    'logger' => [
        'handler' => [
            'class' => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => false,
            ],
        ],
    ],
];
