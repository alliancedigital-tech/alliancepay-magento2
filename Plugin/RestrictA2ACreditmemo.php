<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Plugin;

use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Model\Payment\Validator\RefundRestrictionValidator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

/**
 * Class RestrictA2ACreditmemo.
 */
class RestrictA2ACreditmemo
{
    public function __construct(
        private readonly AllianceOrderRepositoryInterface $allianceOrderRepository,
        private readonly RefundRestrictionValidator $refundRestrictionValidator
    ) {}

    /**
     * @param Order $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanCreditmemo(Order $subject, bool $result): bool
    {
        if (!$result) {
            return false;
        }

        if ($subject->getPayment()->getMethod() !== AlliancePayment::PAYMENT_METHOD_CODE) {
            return $result;
        }

        try {
            $allianceOrder = $this->allianceOrderRepository->getByOrderId(
                (string) $subject->getId()
            );
        } catch (NoSuchEntityException) {
            return $result;
        }

        return $this->refundRestrictionValidator->isRefundRestricted($allianceOrder)
            ? false
            : $result;
    }
}
