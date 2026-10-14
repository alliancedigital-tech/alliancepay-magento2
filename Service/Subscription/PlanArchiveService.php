<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Api\ScheduledPaymentGatewayClientInterface;
use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Alliance\AlliancePay\Exception\SubscriptionException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;

/**
 * PlanArchiveService.
 */
class PlanArchiveService
{
    private const PROVIDER_STATUS_ARCHIVED = 'ARCHIVED';

    public function __construct(
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private readonly ScheduledPaymentGatewayClientInterface $gatewayClient,
        private readonly AuditLogger $auditLogger,
        private readonly AllianceConfig $allianceConfig,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param int $planId
     * @param int|null $adminUserId
     * @return SubscriptionPlanInterface
     * @throws SubscriptionException
     */
    public function archive(int $planId, ?int $adminUserId = null): SubscriptionPlanInterface
    {
        $plan = $this->subscriptionPlanRepository->get($planId);

        $response = $this->gatewayClient->updatePlanStatus([
            'planId' => $plan->getProviderPlanId(),
            'merchantId' => $this->allianceConfig->getMerchantId(),
            'status' => self::PROVIDER_STATUS_ARCHIVED,
        ]);

        if (isset($response['success']) && $response['success'] === false) {
            $this->logger->error('Plan archive failed: ' . json_encode($response));
            throw new SubscriptionException(__('Could not archive subscription plan on the provider side.'));
        }

        $plan->setStatus(SubscriptionPlanInterface::STATUS_CANCELED);
        $this->subscriptionPlanRepository->save($plan);
        $this->auditLogger->log('ARCHIVE_PLAN', $planId, null, $adminUserId);

        return $plan;
    }
}
