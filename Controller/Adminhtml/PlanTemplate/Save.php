<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\PlanTemplate;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterface;
use Alliance\AlliancePay\Api\Data\PlanTemplateInterfaceFactory;
use Alliance\AlliancePay\Api\PlanTemplateRepositoryInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Save.
 */
class Save extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::plan_template';

    public function __construct(
        Context $context,
        private readonly PlanTemplateRepositoryInterface $planTemplateRepository,
        private readonly PlanTemplateInterfaceFactory $planTemplateFactory,
        private readonly DateTime $dateTime
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
        $id = (int)($data['entity_id'] ?? 0);

        try {
            if ($id) {
                $entity = $this->planTemplateRepository->get($id);

                if ($entity->getStatus() === PlanTemplateInterface::STATUS_ARCHIVED) {
                    throw new LocalizedException(__('An archived plan template cannot be edited.'));
                }
            } else {
                $entity = $this->planTemplateFactory->create();
                $entity->setStatus(PlanTemplateInterface::STATUS_ACTIVE);
                $entity->setCreatedAt($this->dateTime->gmtDate());
            }

            $entity->setCode((string)($data['code'] ?? ''));
            $entity->setName((string)($data['name'] ?? ''));
            $entity->setCategoryIndicator($data['category_indicator'] ?? null);
            $entity->setRulesTemplate(array_filter((array)($data['rules_template'] ?? []), 'is_array'));
            $entity->setUpdatedAt($this->dateTime->gmtDate());

            $this->planTemplateRepository->save($entity);
            $this->messageManager->addSuccessMessage(__('The plan template has been saved.'));

            return $resultRedirect->setPath('alliance_pay/plantemplate/index');
        } catch (NoSuchEntityException|LocalizedException|Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('alliance_pay/plantemplate/edit', ['id' => $id]);
        }
    }
}
