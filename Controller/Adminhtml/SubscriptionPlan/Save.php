<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\SubscriptionPlan;

use Alliance\AlliancePay\Service\Subscription\PlanCreationService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save.
 */
class Save extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::subscription_plan';

    public function __construct(
        Context $context,
        private readonly PlanCreationService $planCreationService
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();

        try {
            $rules = array_filter((array)($data['rules'] ?? []), 'is_array');
            $templateId = !empty($data['template_id']) ? (int)$data['template_id'] : null;

            $plan = $this->planCreationService->createPlan(
                $templateId,
                (int)($data['product_id'] ?? 0),
                (string)($data['category_indicator'] ?? ''),
                $rules,
                (string)($data['name'] ?? ''),
                $data['description'] ?? null
            );

            $this->messageManager->addSuccessMessage(__('The subscription plan has been created.'));

            return $resultRedirect->setPath('alliance_pay/subscriptionplan/edit', ['id' => $plan->getEntityId()]);
        } catch (LocalizedException|\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('alliance_pay/subscriptionplan/new');
        }
    }
}
