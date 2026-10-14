<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Config;

use Alliance\AlliancePay\Model\Payment\AlliancePaySubscriptionPayment;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * AlliancePaySubscriptionConfigProvider.
 */
class AlliancePaySubscriptionConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private readonly AlliancePaySubscriptionPayment $alliancePaySubscriptionPayment
    ) {
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                AlliancePaySubscriptionPayment::PAYMENT_METHOD_CODE => [
                    'redirectUrl' => $this->alliancePaySubscriptionPayment->getOrderPlaceRedirectUrl(),
                ],
            ],
        ];
    }
}
