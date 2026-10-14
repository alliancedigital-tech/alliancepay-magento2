<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterface;
use Alliance\AlliancePay\Model\ResourceModel\PlanTemplate\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * PlanTemplateDataProvider.
 */
class PlanTemplateDataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly Registry $coreRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $entity = $this->coreRegistry->registry('alliance_plan_template');

        if (!$entity || !$entity->getEntityId()) {
            return [];
        }

        $data = $entity->getData();
        $data[PlanTemplateInterface::RULES_TEMPLATE] = $entity->getRulesTemplate();

        return [$entity->getEntityId() => $data];
    }
}
