<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class StatusPageType.
 */
class StatusPageType implements OptionSourceInterface
{
    public const DEFAULT_STATUS_PAGE_TYPE = 'STATUS_TIMER_PAGE';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'STATUS_TIMER_PAGE', 'label' => 'STATUS_TIMER_PAGE'],
            ['value' => 'STATUS_REDIRECT_MERCHANT_PAGE', 'label' => 'STATUS_REDIRECT_MERCHANT_PAGE'],
            ['value' => 'STATUS_PAGE', 'label' => 'STATUS_PAGE'],
        ];
    }
}
