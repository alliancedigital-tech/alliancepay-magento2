<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\Data\SubscriptionInterface;
use Alliance\AlliancePay\Api\ScheduledPaymentGatewayClientInterface;
use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Alliance\AlliancePay\Exception\SubscriptionException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;

/**
 * CancellationService.
 */
class CancellationService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly ScheduledPaymentGatewayClientInterface $gatewayClient,
        private readonly SubscriptionPlanCounterService $planCounterService,
        private readonly AuditLogger $auditLogger,
        private readonly AllianceConfig $allianceConfig,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param string $providerOrderId
     * @param int|null $adminUserId
     * @return SubscriptionInterface
     * @throws SubscriptionException
     */
    public function cancel(string $providerOrderId, ?int $adminUserId = null): SubscriptionInterface
    {
        $subscription = $this->subscriptionRepository->getByProviderOrderId($providerOrderId);

        $response = $this->gatewayClient->cancelSubscriptionOrder([
            'hppOrderId' => $providerOrderId,
            'merchantId' => $this->allianceConfig->getMerchantId(),
        ]);

        if (isset($response['success']) && $response['success'] === false) {
            $this->logger->error('Subscription cancellation failed: ' . json_encode($response));
            throw new SubscriptionException(__('Could not cancel subscription on the provider side.'));
        }

        $subscription->setStatus(SubscriptionInterface::STATUS_CANCELED);
        $this->subscriptionRepository->save($subscription);
        $this->planCounterService->decrement((int)$subscription->getPlanId());
        $this->auditLogger->log(
            'CANCEL_SUBSCRIPTION',
            $subscription->getPlanId(),
            null,
            $adminUserId,
            ['subscriptionId' => $subscription->getEntityId()]
        );

        return $subscription;
    }
}
