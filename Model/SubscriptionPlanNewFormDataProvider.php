<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlan\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * SubscriptionPlanNewFormDataProvider.
 */
class SubscriptionPlanNewFormDataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
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
        return [];
    }
}
