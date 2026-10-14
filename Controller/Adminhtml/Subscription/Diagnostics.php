<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\Subscription;

use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;

/**
 * Diagnostics.
 */
class Diagnostics extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::diagnostics';

    public function __construct(
        Context $context,
        private readonly Registry $coreRegistry,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = (int)$this->getRequest()->getParam('id');

        try {
            $subscription = $this->subscriptionRepository->get($id);
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('Subscription does not exist.'));

            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                ->setPath('alliance_pay/subscription/index');
        }

        $this->coreRegistry->register('alliance_subscription', $subscription);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Diagnostics'));

        return $resultPage;
    }
}
