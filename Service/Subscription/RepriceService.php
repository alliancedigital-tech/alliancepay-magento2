<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Api\Data\SubscriptionPlanVersionHistoryInterfaceFactory;
use Alliance\AlliancePay\Api\ScheduledPaymentGatewayClientInterface;
use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Alliance\AlliancePay\Exception\SubscriptionException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\SubscriptionPlanVersionHistoryRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * RepriceService.
 */
class RepriceService
{
    public function __construct(
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private readonly ScheduledPaymentGatewayClientInterface $gatewayClient,
        private readonly SubscriptionPlanVersionHistoryInterfaceFactory $versionHistoryFactory,
        private readonly SubscriptionPlanVersionHistoryRepository $versionHistoryRepository,
        private readonly RulesValidator $rulesValidator,
        private readonly AuditLogger $auditLogger,
        private readonly AllianceConfig $allianceConfig,
        private readonly DateTime $dateTime,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param int $planId
     * @param array $newRules
     * @param DateTimeInterface $appliesFrom
     * @param string $reason
     * @param bool $customersNotifiedConfirmed
     * @param int|null $adminUserId
     * @return SubscriptionPlanInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws SubscriptionException
     */
    public function reprice(
        int $planId,
        array $newRules,
        DateTimeInterface $appliesFrom,
        string $reason,
        bool $customersNotifiedConfirmed,
        ?int $adminUserId = null
    ): SubscriptionPlanInterface {
        if (!$customersNotifiedConfirmed) {
            throw new SubscriptionException(
                __('You must confirm that affected customers have been or will be notified.')
            );
        }

        $minAppliesFrom = new DateTimeImmutable('+' . $this->allianceConfig->getRepriceLeadTimeDays() . ' days');

        if ($appliesFrom < $minAppliesFrom) {
            throw new SubscriptionException(
                __(
                    'New rules cannot take effect earlier than %1 days from now.',
                    $this->allianceConfig->getRepriceLeadTimeDays()
                )
            );
        }

        $this->rulesValidator->validate($newRules);

        $plan = $this->subscriptionPlanRepository->get($planId);
        $oldRules = $plan->getRules();
        $oldVersionId = $plan->getVersionId();

        $updateResponse = $this->gatewayClient->updatePlan([
            'planId' => $plan->getProviderPlanId(),
            'merchantId' => $this->allianceConfig->getMerchantId(),
            'rules' => $newRules,
            'appliesDateTimeFrom' => $appliesFrom->format(DateTimeInterface::ATOM),
        ]);

        if (empty($updateResponse['versionId'])) {
            $this->logger->error('Reprice plan update failed: ' . json_encode($updateResponse));
            throw new SubscriptionException(__('Could not update subscription plan on the provider side.'));
        }

        $newVersionId = (string)$updateResponse['versionId'];

        $versionResponse = $this->gatewayClient->updatePlanVersion([
            'planId' => $plan->getProviderPlanId(),
            'versionId' => $newVersionId,
            'infiniteVersion' => true,
        ]);

        if (isset($versionResponse['success']) && $versionResponse['success'] === false) {
            $plan->setSyncStatus(SubscriptionPlanInterface::SYNC_STATUS_ERROR);
            $plan->setSyncError((string)($versionResponse['message'] ?? 'infinite schedule call failed'));
            $this->subscriptionPlanRepository->save($plan);
            $this->auditLogger->log('REPRICE_INFINITE_SCHEDULE_FAILED', $planId, null, $adminUserId, $versionResponse);
            throw new SubscriptionException(
                __('Plan rules were updated but the infinite schedule call failed. Manual reconciliation is required.')
            );
        }

        $plan->setRules($newRules);
        $plan->setVersionId($newVersionId);
        $plan->setSyncStatus(SubscriptionPlanInterface::SYNC_STATUS_SYNCED);
        $this->subscriptionPlanRepository->save($plan);

        $history = $this->versionHistoryFactory->create();
        $history->setPlanId($planId);
        $history->setOldVersionId($oldVersionId);
        $history->setNewVersionId($newVersionId);
        $history->setOldRules($oldRules);
        $history->setNewRules($newRules);
        $history->setAppliesFrom($appliesFrom->format('Y-m-d H:i:s'));
        $history->setReason($reason);
        $history->setCreatedBy($adminUserId);
        $history->setCreatedAt($this->dateTime->gmtDate());
        $this->versionHistoryRepository->save($history);

        $this->auditLogger->log('REPRICE', $planId, null, $adminUserId, ['reason' => $reason]);

        return $plan;
    }
}
