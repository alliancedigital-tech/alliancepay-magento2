<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlanVersionHistory;

use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlanVersionHistoryResource;
use Alliance\AlliancePay\Model\SubscriptionPlanVersionHistory;
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
        $this->_init(SubscriptionPlanVersionHistory::class, SubscriptionPlanVersionHistoryResource::class);
    }
}
