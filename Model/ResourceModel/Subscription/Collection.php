<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel\Subscription;

use Alliance\AlliancePay\Model\ResourceModel\SubscriptionResource;
use Alliance\AlliancePay\Model\Subscription;
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
        $this->_init(Subscription::class, SubscriptionResource::class);
    }
}
