<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\PlanTemplate;

use Alliance\AlliancePay\Api\PlanTemplateRepositoryInterface;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlan\CollectionFactory as SubscriptionPlanCollectionFactory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Delete.
 */
class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::plan_template';

    public function __construct(
        Context $context,
        private readonly PlanTemplateRepositoryInterface $planTemplateRepository,
        private readonly SubscriptionPlanCollectionFactory $subscriptionPlanCollectionFactory
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
            $entity = $this->planTemplateRepository->get($id);
            $referencingPlans = $this->subscriptionPlanCollectionFactory->create()
                ->addFieldToFilter('template_id', $id)
                ->getSize();

            if ($referencingPlans > 0) {
                throw new LocalizedException(
                    __('This template is used by at least one subscription plan and cannot be deleted.')
                );
            }

            $this->planTemplateRepository->delete($entity);
            $this->messageManager->addSuccessMessage(__('The plan template has been deleted.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('alliance_pay/plantemplate/index');
    }
}
