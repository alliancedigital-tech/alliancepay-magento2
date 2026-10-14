<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * IntervalUnit.
 */
class IntervalUnit implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'DAY', 'label' => __('Day')],
            ['value' => 'WEEK', 'label' => __('Week')],
            ['value' => 'MONTH', 'label' => __('Month')],
        ];
    }
}
