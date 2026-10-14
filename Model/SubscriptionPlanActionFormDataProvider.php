<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlan\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * SubscriptionPlanActionFormDataProvider.
 */
class SubscriptionPlanActionFormDataProvider extends AbstractDataProvider
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
        $entity = $this->coreRegistry->registry('alliance_subscription_plan');

        if (!$entity || !$entity->getEntityId()) {
            return [];
        }

        return [$entity->getEntityId() => ['id' => $entity->getEntityId()]];
    }
}
