<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel\SubscriptionAuditLog;

use Alliance\AlliancePay\Model\ResourceModel\SubscriptionAuditLogResource;
use Alliance\AlliancePay\Model\SubscriptionAuditLog;
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
        $this->_init(SubscriptionAuditLog::class, SubscriptionAuditLogResource::class);
    }
}
