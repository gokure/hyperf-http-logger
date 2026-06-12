<?php

declare(strict_types=1);
/**
 * This file is part of gokure/hyperf-cors.
 *
 * @link     https://github.com/gokure/hyperf-cors
 * @document https://github.com/gokure/hyperf-cors/blob/main/README.md
 * @contact  gokure@gmail.com
 * @license  https://github.com/hyperf/hyperf-cors/blob/main/LICENSE
 */
return [
    /*
     * Matches the log request method. `['*']` allows all methods.
     */
    'allowed_methods' => ['*'],

    /*
     * Matches the log context of request method. `['*']` allows all methods.
     */
    'allowed_context_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],

    /*
     * Determine bypass logging when the true returned.
     * For example, you can ignore logging for given user agent.
     *
     *     function ($response, $request) {
     *         return $request->getHeaderLine('user-agent') === 'SLBHealthCheck';
     *     }
     */
    'bypass_function' => function ($response, $request) {},

    /*
     * Sets the logger instance.
     */
    'logger' => [
        'name' => 'hyperf',
        'channel' => 'default',
    ],
];
