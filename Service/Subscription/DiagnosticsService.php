<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Logger\Logger;
use Throwable;

/**
 * DiagnosticsService.
 */
class DiagnosticsService
{
    public function __construct(
        private readonly GatewayClientInterface $gatewayClient,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param string $providerOrderId
     * @return array
     */
    public function getFreshOperations(string $providerOrderId): array
    {
        try {
            return $this->gatewayClient->getOperationStatus($providerOrderId);
        } catch (Throwable $exception) {
            $this->logger->error('Subscription diagnostics fetch failed: ' . $exception->getMessage());

            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }
}
