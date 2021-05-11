# A HTTP Requests Logger for Hyperf

A middleware which can log incoming requests and responses to the log.

## Installation

Require the `gokure/hypref-http-logger` package in your `composer.json` and update your dependencies:

```sh
composer require gokure/hypref-http-logger
```

Optionally you can publish the configuration file with:

```sh
php bin/hyperf.php vendor:publish gokure/hypref-http-logger
```

## Usage

To allow HTTP Logger in your application, add the `HttpLoggerMiddleware` middleware at the top of the property of `config/autoload/middlewares.php` file in the config (see Configuration below):

```php
'http' => [
    \Gokure\HttpLogger\HttpLoggerMiddleware::class,
    ...
],
```

Also, to allow HTTP Logger for exception responses, you need add the `HttpLoggerExceptionHandler` handler at the top of the property of `config/autoload/exceptions.php` file:

```php
'handler' => [
    'http' => [
        Gokure\HttpLogger\HttpLoggerExceptionHandler::class,
        ...
    ],
],
```

## License

Released under the MIT License, see [LICENSE](LICENSE).
