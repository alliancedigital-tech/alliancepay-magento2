<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Processor;

use Alliance\AlliancePay\Api\RefundManagerInterface;
use Alliance\AlliancePay\Exception\RefundException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Payment\RefundService;

/**
 * Class RefundProcessor.
 */
class RefundProcessor implements RefundManagerInterface
{
    public function __construct(
        private readonly RefundService $refundService,
        private readonly Logger $logger
    ) {}

    /**
     * @param string $operationId
     * @param float $refundAmount
     * @param string $callbackUrl
     * @return array
     */
    public function processRefund(string $operationId, float $refundAmount, string $callbackUrl): array
    {
        try {
            $refundResult = $this->refundService->refund(
                $operationId,
                $refundAmount,
                $callbackUrl
            );

            return $refundResult;
        } catch (RefundException $e) {
            $this->logger->error($e->getMessage());

            return ['success' => false, 'message' => __('Refund failed')];
        }
    }
}
