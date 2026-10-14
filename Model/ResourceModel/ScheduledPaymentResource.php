<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * ScheduledPaymentResource.
 */
class ScheduledPaymentResource extends AbstractDb
{
    private const TABLE_NAME = 'alliance_subscription_scheduled_payment';

    private const ID = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::ID);
    }
}
