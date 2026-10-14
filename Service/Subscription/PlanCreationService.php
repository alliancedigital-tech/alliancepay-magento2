<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterfaceFactory;
use Alliance\AlliancePay\Api\ScheduledPaymentGatewayClientInterface;
use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Alliance\AlliancePay\Exception\PlanCreationException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Service\Payment\Service\ServiceAbstract;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * PlanCreationService.
 */
class PlanCreationService extends ServiceAbstract
{
    public function __construct(
        private readonly ScheduledPaymentGatewayClientInterface $gatewayClient,
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private readonly SubscriptionPlanInterfaceFactory $subscriptionPlanFactory,
        private readonly PlanKeyGenerator $planKeyGenerator,
        private readonly RulesValidator $rulesValidator,
        private readonly AuditLogger $auditLogger,
        private readonly AllianceConfig $allianceConfig,
        private readonly DateTime $dateTime,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param int|null $templateId
     * @param int $productId
     * @param string $categoryIndicator
     * @param array $rules
     * @param string $name
     * @param string|null $description
     * @return SubscriptionPlanInterface
     * @throws LocalizedException
     * @throws PlanCreationException
     */
    public function createPlan(
        ?int $templateId,
        int $productId,
        string $categoryIndicator,
        array $rules,
        string $name,
        ?string $description
    ): SubscriptionPlanInterface {
        $this->rulesValidator->validate($rules);

        $planKey = $this->planKeyGenerator->generate(
            (string)$this->allianceConfig->getMerchantId(),
            $productId,
            $templateId,
            $categoryIndicator,
            $rules
        );

        try {
            $existingPlan = $this->subscriptionPlanRepository->getByPlanKey($planKey);
            $this->auditLogger->log('CREATE_PLAN_DEDUPED', $existingPlan->getEntityId(), $productId, null);

            return $existingPlan;
        } catch (NoSuchEntityException) {
        }

        $createResponse = $this->gatewayClient->createPlan([
            'name' => $name,
            'description' => $description,
            'categorySubcategoryIndicator' => $categoryIndicator,
            'merchantId' => $this->allianceConfig->getMerchantId(),
            'rules' => $rules,
        ]);

        if (empty($createResponse['planId'])) {
            $this->logger->error('Subscription plan creation failed: ' . json_encode($createResponse));
            $this->auditLogger->log('CREATE_PLAN_FAILED', null, $productId, null, $createResponse);
            throw new PlanCreationException(__('Could not create subscription plan on the provider side.'));
        }

        $plan = $this->subscriptionPlanFactory->create();
        $plan->setProviderPlanId((string)$createResponse['planId']);
        $plan->setProductId($productId);
        $plan->setTemplateId($templateId);
        $plan->setPlanKey($planKey);
        $plan->setMerchantId((string)$this->allianceConfig->getMerchantId());
        $plan->setName($name);
        $plan->setDescription($description);
        $plan->setCategoryIndicator($categoryIndicator);
        $plan->setRules($rules);
        $plan->setVersionId(isset($createResponse['versionId']) ? (string)$createResponse['versionId'] : null);
        $plan->setIsActive(true);
        $plan->setActiveSubscriptionsCount(0);
        $plan->setCreatedAt($this->dateTime->gmtDate());

        $versionResponse = $this->gatewayClient->updatePlanVersion([
            'planId' => $createResponse['planId'],
            'versionId' => $createResponse['versionId'] ?? null,
            'infiniteVersion' => true,
        ]);

        if (empty($versionResponse['success']) && isset($versionResponse['message'])) {
            $plan->setStatus(SubscriptionPlanInterface::STATUS_ERROR);
            $plan->setSyncStatus(SubscriptionPlanInterface::SYNC_STATUS_ERROR);
            $plan->setSyncError((string)$versionResponse['message']);
            $this->subscriptionPlanRepository->save($plan);
            $this->auditLogger->log(
                'CREATE_PLAN_INFINITE_SCHEDULE_FAILED',
                $plan->getEntityId(),
                $productId,
                null,
                $versionResponse
            );
            $this->logger->error(
                'Subscription plan infinite schedule call failed: ' . json_encode($versionResponse)
            );

            return $plan;
        }

        $plan->setStatus(SubscriptionPlanInterface::STATUS_ACTIVE);
        $plan->setSyncStatus(SubscriptionPlanInterface::SYNC_STATUS_SYNCED);
        $this->subscriptionPlanRepository->save($plan);
        $this->auditLogger->log('CREATE_PLAN', $plan->getEntityId(), $productId, null);

        return $plan;
    }
}
