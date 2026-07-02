<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Model\Order;

/**
 * Class InitializeCommand.
 */
class InitializeCommand implements CommandInterface
{
    /**
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject): void
    {
        $stateObject = $commandSubject['stateObject'];
        $stateObject->setState(Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus(Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
    }
}
