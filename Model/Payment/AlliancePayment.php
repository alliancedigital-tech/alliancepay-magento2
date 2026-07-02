<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment;

use Magento\Framework\UrlInterface;

/**
 * Class AlliancePayment.
 */
class AlliancePayment
{
    public const PAYMENT_METHOD_CODE = 'alliance_pay';
    public const PAYMENT_METHODS = ['CARD', 'APPLE_PAY', 'GOOGLE_PAY'];
    public const HPP_PAY_TYPE_PURCHASE = 'PURCHASE';
    public const HPP_PAY_TYPE_A2A = 'A2A';
    public const OPERATION_TYPE_PURCHASE = 'PURCHASE';
    public const OPERATION_TYPE_A2A = 'ACCOUNT_2_ACCOUNT';
    public const DIRECT_TYPE_BANK_LINK = 'BANK_LINK';
    public const PRIORITY_BANK_CODE = 'ALL_BANKS';

    public function __construct(
        private readonly UrlInterface $urlBuilder
    ) {}

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl(): string
    {
        return $this->urlBuilder->getUrl('alliance_pay/payment/redirect');
    }

    /**
     * @return string
     */
    public function getSuccessUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout/onepage/success');
    }

    /**
     * @return string
     */
    public function getFailUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout/cart/index');
    }

    /**
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->urlBuilder->getUrl('', ['_direct' => 'rest/V1/alliance_pay/callback']);
    }
}
