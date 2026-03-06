<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Payment\Service;

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

        return (int) $amount;
    }
}
