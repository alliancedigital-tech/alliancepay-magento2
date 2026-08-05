<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PaymentType.
 */
class PaymentType implements OptionSourceInterface
{
    public const DEFAULT_PAYMENT_TYPE = 'PURCHASE';

    public const PREAUTH_PAYMENT_TYPE = 'PREAUTH';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'PURCHASE', 'label' => 'PURCHASE'],
            ['value' => 'A2A', 'label' => 'A2A'],
            ['value' => 'PREAUTH', 'label' => 'PREAUTH'],
        ];
    }
}
