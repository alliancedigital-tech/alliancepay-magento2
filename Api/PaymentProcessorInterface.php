<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * PaymentProcessorInterface.
 */
interface PaymentProcessorInterface
{
    /**
     * @param OrderInterface $order
     * @return array
     */
    public function process(OrderInterface $order): array;
}
