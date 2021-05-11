<?php

declare(strict_types=1);

namespace Gokure\HttpLogger;

use Hyperf\Contract\StdoutLoggerInterface;
use Monolog\Logger as MonoLogger;

class Logger extends MonoLogger implements StdoutLoggerInterface
{
}
