<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Payment\Service;

use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ServiceAbstract.
 */
abstract class ServiceAbstract
{
    /**
     * @return string
     */
    public function generateMerchantRequestId(): string
    {
        return uniqid();
    }

    /**
     * @param float $amount
     * @return int
     */
    public function prepareCoinAmount(float $amount)
    {
        if (!empty($amount)) {
            $amount = $amount * 100;
        }

        return (int) round($amount);
    }

    /**
     * @param string $code
     * @return int
     * @throws LocalizedException
     */
    public function getCurrencyCode(string $code): int
    {
        if (!array_key_exists($code, AlliancePayment::CURRENCY_CODE_MAP)) {
            throw new LocalizedException(
                __('You cannot place an order with this currency: %1.', $code)
            );
        }

        return AlliancePayment::CURRENCY_CODE_MAP[$code];
    }
}
