<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel\AllianceOrder;

use Alliance\AlliancePay\Model\AllianceOrder;
use Alliance\AlliancePay\Model\ResourceModel\AllianceOrderResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            AllianceOrder::class,
            AllianceOrderResource::class
        );
    }
}
