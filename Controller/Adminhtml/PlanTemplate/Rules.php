<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\PlanTemplate;

use Alliance\AlliancePay\Api\PlanTemplateRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Rules.
 */
class Rules extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::plan_template';

    public function __construct(
        Context $context,
        private readonly PlanTemplateRepositoryInterface $planTemplateRepository,
        private readonly JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();
        $id = (int)$this->getRequest()->getParam('id');

        try {
            $entity = $this->planTemplateRepository->get($id);

            return $result->setData(['rules_template' => $entity->getRulesTemplate()]);
        } catch (NoSuchEntityException) {
            return $result->setData(['rules_template' => []]);
        }
    }
}
