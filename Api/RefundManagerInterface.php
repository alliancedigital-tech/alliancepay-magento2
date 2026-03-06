<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

/**
 * RefundManagerInterface.
 */
interface RefundManagerInterface
{
    /**
     * @param string $operationId
     * @param float $refundAmount
     * @param string $callbackUrl
     * @return array
     */
    public function processRefund(string $operationId, float $refundAmount, string $callbackUrl): array;
}
