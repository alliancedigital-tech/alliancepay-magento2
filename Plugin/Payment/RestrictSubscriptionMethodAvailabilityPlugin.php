<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Plugin\Payment;

use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Model\Payment\AlliancePaySubscriptionPayment;
use Magento\Payment\Model\Method\Adapter;
use Magento\Quote\Api\Data\CartInterface;

/**
 * RestrictSubscriptionMethodAvailabilityPlugin.
 */
class RestrictSubscriptionMethodAvailabilityPlugin
{
    /**
     * @param Adapter $subject
     * @param callable $proceed
     * @param CartInterface|null $quote
     * @return bool
     */
    public function aroundIsAvailable(Adapter $subject, callable $proceed, ?CartInterface $quote = null): bool
    {
        $code = $subject->getCode();

        if ($code !== AlliancePaySubscriptionPayment::PAYMENT_METHOD_CODE
            && $code !== AlliancePayment::PAYMENT_METHOD_CODE) {
            return $proceed($quote);
        }

        if (!$proceed($quote) || $quote === null) {
            return false;
        }

        $items = $quote->getAllVisibleItems();
        $hasSubscriptionItem = false;

        foreach ($items as $item) {
            if ($item->getBuyRequest()->getData('alliance_purchase_mode') === 'subscribe') {
                $hasSubscriptionItem = true;
                break;
            }
        }

        if ($code === AlliancePaySubscriptionPayment::PAYMENT_METHOD_CODE) {
            return !$quote->getCustomerIsGuest() && count($items) === 1 && $hasSubscriptionItem;
        }

        return !$hasSubscriptionItem;
    }
}
