<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment;

use Magento\Framework\UrlInterface;

/**
 * AlliancePaySubscriptionPayment.
 */
class AlliancePaySubscriptionPayment
{
    public const PAYMENT_METHOD_CODE = 'alliance_pay_subscription';
    public const DIRECT_TYPE_REDIRECT = 'REDIRECT';

    public function __construct(
        private readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl(): string
    {
        return $this->urlBuilder->getUrl('alliance_pay/payment/subscriptionredirect');
    }
}
