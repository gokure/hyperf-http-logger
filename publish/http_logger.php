<?php

declare(strict_types=1);

return [
    /**
     * Matches the log request method. `['*']` allows all methods.
     */
    'allowed_methods' => ['*'],

    /**
     * Matches the log context of request method. `['*']` allows all methods.
     */
    'allowed_context_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],

    /**
     * Determine bypass logging when the true returned.
     * For example, you can ignore logging for given user agent.
     *
     *     function ($response, $request) {
     *         return $request->getHeaderLine('user-agent') === 'SLBHealthCheck';
     *     }
     */
    'bypass_function' => function ($response, $request) {},

    /**
     * Sets the logger instance.
     */
    'logger' => [
        'name' => 'hyperf',
        'group' => 'default',
    ],
];
