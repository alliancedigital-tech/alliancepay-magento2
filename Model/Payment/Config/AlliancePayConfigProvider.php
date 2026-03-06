<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Config;

use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class AlliancePayConfigProvider.
 */
class AlliancePayConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private readonly AlliancePayment $alliancePayment,
        private readonly AllianceConfig $allianceConfig,
    ) {
    }

    /**
     * @return \array[][]
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                AlliancePayment::PAYMENT_METHOD_CODE => [
                    'active' => $this->alliancePayment->isActive(),
                    'title' => $this->allianceConfig->getTitle(),
                    'redirectUrl' => $this->alliancePayment->getOrderPlaceRedirectUrl(),
                ]
            ]
        ];
    }
}
