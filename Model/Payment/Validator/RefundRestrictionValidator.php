<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Validator;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;

/**
 * Class RefundRestrictionValidator.
 */
class RefundRestrictionValidator
{
    /**
     * @param AllianceOrderInterface $allianceOrder
     * @return bool
     */
    public function isRefundRestricted(AllianceOrderInterface $allianceOrder): bool
    {
        return (int) $allianceOrder->getTransactionType() === AllianceOrderInterface::TRANSACTION_TYPE_NO_REFUND
            && $allianceOrder->getHppPayType() === AlliancePayment::HPP_PAY_TYPE_A2A;
    }
}
