<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel\ScheduledPayment;

use Alliance\AlliancePay\Model\ResourceModel\ScheduledPaymentResource;
use Alliance\AlliancePay\Model\ScheduledPayment;
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
        $this->_init(ScheduledPayment::class, ScheduledPaymentResource::class);
    }
}
