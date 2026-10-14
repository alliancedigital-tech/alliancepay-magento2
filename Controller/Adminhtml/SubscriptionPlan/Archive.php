<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\SubscriptionPlan;

use Alliance\AlliancePay\Service\Subscription\PlanArchiveService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Archive.
 */
class Archive extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::plan_archive';

    public function __construct(
        Context $context,
        private readonly PlanArchiveService $planArchiveService
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $planId = (int)$this->getRequest()->getParam('id');

        try {
            $this->planArchiveService->archive($planId, (int)$this->_auth->getUser()->getId());
            $this->messageManager->addSuccessMessage(__('The subscription plan has been archived.'));
        } catch (LocalizedException|\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('alliance_pay/subscriptionplan/index');
    }
}
