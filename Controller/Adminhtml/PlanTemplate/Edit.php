<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\PlanTemplate;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterfaceFactory;
use Alliance\AlliancePay\Api\PlanTemplateRepositoryInterface;
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
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::plan_template';

    public function __construct(
        Context $context,
        private readonly Registry $coreRegistry,
        private readonly PlanTemplateRepositoryInterface $planTemplateRepository,
        private readonly PlanTemplateInterfaceFactory $planTemplateFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $entity = $this->planTemplateRepository->get($id);
            } catch (NoSuchEntityException) {
                $this->messageManager->addErrorMessage(__('Plan template does not exist.'));

                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                    ->setPath('alliance_pay/plantemplate/index');
            }
        } else {
            $entity = $this->planTemplateFactory->create();
        }

        $this->coreRegistry->register('alliance_plan_template', $entity);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Plan Template') : __('New Plan Template')
        );

        return $resultPage;
    }
}
