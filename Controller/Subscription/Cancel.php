<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Subscription;

use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Alliance\AlliancePay\Service\Subscription\CancellationService;
use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Cancel.
 */
class Cancel extends Action implements AccountInterface, HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly Session $customerSession,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly CancellationService $cancellationService
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

            if ((int)$subscription->getCustomerId() !== (int)$this->customerSession->getCustomerId()) {
                throw new LocalizedException(__('This subscription does not belong to your account.'));
            }

            $this->cancellationService->cancel((string)$subscription->getProviderOrderId());
            $this->messageManager->addSuccessMessage(__('Your subscription has been canceled.'));
        } catch (LocalizedException|\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('alliance_pay/subscription/index');
    }
}
