<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PreAuthExpDate.
 */
class PreAuthExpDate implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '2h', 'label' => __('2 hours')],
            ['value' => '4h', 'label' => __('4 hours')],
            ['value' => '6h', 'label' => __('6 hours')],
            ['value' => '12h', 'label' => __('12 hours')],
            ['value' => '1d', 'label' => __('1 day')],
            ['value' => '2d', 'label' => __('2 days')],
            ['value' => '7d', 'label' => __('7 days')],
            ['value' => '14d', 'label' => __('14 days')],
            ['value' => '28d', 'label' => __('28 days')],
        ];
    }
}
