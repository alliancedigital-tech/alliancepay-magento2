<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\Subscription;

use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Alliance\AlliancePay\Service\Subscription\CancellationService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Cancel.
 */
class Cancel extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::subscription_cancel';

    public function __construct(
        Context $context,
        private readonly CancellationService $cancellationService,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository
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
            $subscription = $this->subscriptionRepository->get($id);
            $this->cancellationService->cancel(
                (string)$subscription->getProviderOrderId(),
                (int)$this->_auth->getUser()->getId()
            );
            $this->messageManager->addSuccessMessage(__('The subscription has been canceled.'));
        } catch (LocalizedException|\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('alliance_pay/subscription/index');
    }
}
