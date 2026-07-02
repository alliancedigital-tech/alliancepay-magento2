<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Command;

use Magento\Payment\Gateway\CommandInterface;

/**
 * Class CaptureCommand.
 */
class CaptureCommand implements CommandInterface
{
    /**
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject): void
    {
        // no-op: capture is performed via gateway callback in CallbackService
    }
}
