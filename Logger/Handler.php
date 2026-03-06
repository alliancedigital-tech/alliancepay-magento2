<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class Handler.
 */
class Handler extends Base
{
    protected $loggerType = Logger::INFO;

    protected $fileName = '/var/log/vendor_module.log';
}
