<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AllianceOrderResource.
 */
class AllianceOrderResource extends AbstractDb
{
    private const TABLE_NAME = 'alliance_checkout_integration_order';

    private const ID = 'entity_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::ID);
    }
}
