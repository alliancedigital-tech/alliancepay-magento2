<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\SubscriptionPlan;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Alliance\AlliancePay\Service\Subscription\AuditLogger;
use Alliance\AlliancePay\Service\Subscription\PlanCreationService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * ReplaceForNewCustomers.
 */
class ReplaceForNewCustomers extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::plan_replace_new_customers';

    public function __construct(
        Context $context,
        private readonly PlanCreationService $planCreationService,
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private readonly AuditLogger $auditLogger
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $oldPlanId = (int)$this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPostValue();

        try {
            $oldPlan = $this->subscriptionPlanRepository->get($oldPlanId);
            $rules = array_filter((array)($data['rules'] ?? []), 'is_array');

            $newPlan = $this->planCreationService->createPlan(
                $oldPlan->getTemplateId(),
                (int)$oldPlan->getProductId(),
                (string)$oldPlan->getCategoryIndicator(),
                $rules,
                (string)($data['name'] ?? $oldPlan->getName()),
                $data['description'] ?? $oldPlan->getDescription()
            );

            $oldPlan->setIsActive(false);
            $this->subscriptionPlanRepository->save($oldPlan);

            $this->auditLogger->log(
                'REPLACE_NEW_CUSTOMERS_ONLY',
                $newPlan->getEntityId(),
                (int)$oldPlan->getProductId(),
                (int)$this->_auth->getUser()->getId(),
                ['oldPlanId' => $oldPlanId, 'newPlanId' => $newPlan->getEntityId()]
            );

            $this->messageManager->addSuccessMessage(
                __('A new plan has been created for new customers. The old plan remains active for existing subscribers.')
            );

            return $resultRedirect->setPath('alliance_pay/subscriptionplan/edit', ['id' => $newPlan->getEntityId()]);
        } catch (LocalizedException|\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('alliance_pay/subscriptionplan/edit', ['id' => $oldPlanId]);
        }
    }
}
