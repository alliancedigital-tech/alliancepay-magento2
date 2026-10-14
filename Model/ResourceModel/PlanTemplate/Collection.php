<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel\PlanTemplate;

use Alliance\AlliancePay\Model\PlanTemplate;
use Alliance\AlliancePay\Model\ResourceModel\PlanTemplateResource;
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
        $this->_init(PlanTemplate::class, PlanTemplateResource::class);
    }
}
