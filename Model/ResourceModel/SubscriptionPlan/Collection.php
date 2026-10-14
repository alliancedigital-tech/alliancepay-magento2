<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlan;

use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlanResource;
use Alliance\AlliancePay\Model\SubscriptionPlan;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SubscriptionPlan::class, SubscriptionPlanResource::class);
    }
}
