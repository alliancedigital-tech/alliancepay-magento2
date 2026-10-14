<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\SubscriptionPlan;

use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;

/**
 * Edit.
 */
class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::subscription_plan';

    public function __construct(
        Context $context,
        private readonly Registry $coreRegistry,
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository
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
            $entity = $this->subscriptionPlanRepository->get($id);
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('Subscription plan does not exist.'));

            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                ->setPath('alliance_pay/subscriptionplan/index');
        }

        $this->coreRegistry->register('alliance_subscription_plan', $entity);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Plan #%1', $entity->getEntityId()));

        return $resultPage;
    }
}
