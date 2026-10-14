<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

/**
 * GatewayClientInterface.
 */
interface GatewayClientInterface
{
    /**
     * @param array $orderData
     * @return array
     */
    public function createOrder(array $orderData): array;

    /**
     * @param string $hppOrderId
     * @return array
     */
    public function getOperationStatus(string $hppOrderId): array;

    /**
     * @param array $refundData
     * @return array
     */
    public function refund(array $refundData): array;

    /**
     * @param array $completionData
     * @return array
     */
    public function completion(array $completionData): array;
}
