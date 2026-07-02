<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Command;

use Magento\Payment\Gateway\CommandInterface;

/**
 * Class AuthorizeCommand.
 */
class AuthorizeCommand implements CommandInterface
{
    /**
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject): void
    {
        // no-op: payment is authorized via HPP redirect and gateway callback
    }
}
