<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\ScheduledPayment;

use Alliance\AlliancePay\Service\Subscription\ManualPaymentTriggerService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * RunNow.
 */
class RunNow extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::scheduled_payment_run_now';

    public function __construct(
        Context $context,
        private readonly ManualPaymentTriggerService $manualPaymentTriggerService
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $id = (int)$this->getRequest()->getParam('id');

        try {
            $this->manualPaymentTriggerService->run($id, (int)$this->_auth->getUser()->getId());
            $this->messageManager->addSuccessMessage(__('The recurring payment has been triggered.'));
        } catch (LocalizedException|\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('alliance_pay/scheduledpayment/index');
    }
}
