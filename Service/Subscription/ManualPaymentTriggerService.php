<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\ScheduledPaymentGatewayClientInterface;
use Alliance\AlliancePay\Api\ScheduledPaymentRepositoryInterface;
use Alliance\AlliancePay\Exception\SubscriptionException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;

/**
 * ManualPaymentTriggerService.
 */
class ManualPaymentTriggerService
{
    public function __construct(
        private readonly ScheduledPaymentRepositoryInterface $scheduledPaymentRepository,
        private readonly ScheduledPaymentGatewayClientInterface $gatewayClient,
        private readonly AuditLogger $auditLogger,
        private readonly AllianceConfig $allianceConfig,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param int $scheduledPaymentId
     * @param int|null $adminUserId
     * @return array
     * @throws SubscriptionException
     */
    public function run(int $scheduledPaymentId, ?int $adminUserId = null): array
    {
        $scheduledPayment = $this->scheduledPaymentRepository->get($scheduledPaymentId);

        $response = $this->gatewayClient->triggerRecurringPayment([
            'hppOrderId' => $scheduledPayment->getProviderOrderId(),
            'scheduledPaymentId' => $scheduledPayment->getScheduledPaymentId(),
            'merchantId' => $this->allianceConfig->getMerchantId(),
        ]);

        if (isset($response['success']) && $response['success'] === false) {
            $this->logger->error('Manual recurring payment trigger failed: ' . json_encode($response));
            throw new SubscriptionException(__('Could not trigger the recurring payment on the provider side.'));
        }

        $this->auditLogger->log(
            'MANUAL_TRIGGER',
            null,
            null,
            $adminUserId,
            ['scheduledPaymentId' => $scheduledPaymentId]
        );

        return $response;
    }
}
