<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Command;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use InvalidArgumentException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Class RefundAmountResolver.
 */
class RefundAmountResolver
{
    private const REFUND_COUNTED_STATUSES = [
        AllianceOrderInterface::ORDER_STATUS_SUCCESS,
        AllianceOrderInterface::ORDER_STATUS_PENDING,
    ];

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Logger $logger
    ) {}

    /**
     * @param AllianceOrderInterface $allianceOrder
     * @param float $refundAmount Current refund amount in the order currency
     * @param float $orderGrandTotal Order grand total in the order currency
     * @param float $alreadyRefunded Amount refunded before the current refund, in the order currency
     * @return float Amount to send to the bank (bank currency)
     * @throws CommandException
     */
    public function resolve(
        AllianceOrderInterface $allianceOrder,
        float $refundAmount,
        float $orderGrandTotal,
        float $alreadyRefunded
    ): float {
        $conversionRate = $allianceOrder->getConversionRate();

        if ($conversionRate === null || $conversionRate <= 0) {
            return $refundAmount;
        }

        [$bankTotal, $bankRefunded] = $this->getBankAmounts($allianceOrder);
        $hasBankData = $bankTotal > 0;

        $bankRefunded = max($bankRefunded, $this->toCoins($alreadyRefunded * $conversionRate));

        $remaining = $hasBankData ? $bankTotal - $bankRefunded : null;
        if ($remaining !== null && $remaining <= 0) {
            throw new CommandException(__('Nothing left to refund.'));
        }

        $isFinalRefund = $hasBankData
            && $this->toCoins($alreadyRefunded) + $this->toCoins($refundAmount) >= $this->toCoins($orderGrandTotal);
        if ($isFinalRefund) {
            return $remaining / 100;
        }

        $requested = $this->toCoins($refundAmount * $conversionRate);

        return ($remaining === null ? $requested : min($requested, $remaining)) / 100;
    }

    /**
     * @param AllianceOrderInterface $allianceOrder
     * @return array
     */
    public function getOperations(AllianceOrderInterface $allianceOrder): array
    {
        $callbackData = $allianceOrder->getCallbackData();
        if (empty($callbackData)) {
            return [];
        }

        try {
            $data = $this->serializer->unserialize($callbackData);
        } catch (InvalidArgumentException $e) {
            $this->logger->error(sprintf(
                'Failed to unserialize callback_data for order %s: %s',
                (string)$allianceOrder->getOrderId(),
                $e->getMessage()
            ));

            return [];
        }

        $operations = $data['operations'] ?? [];
        if (empty($operations) && isset($data['operation'])) {
            $operations = [$data['operation']];
        }

        return $operations;
    }

    /**
     * @param AllianceOrderInterface $allianceOrder
     * @return int[]
     */
    private function getBankAmounts(AllianceOrderInterface $allianceOrder): array
    {
        $captured = 0;
        $completion = 0;
        $refunded = 0;

        foreach ($this->getOperations($allianceOrder) as $operation) {
            $type = $operation['type'] ?? '';
            $status = $operation['status'] ?? '';
            $coins = (int)($operation['coinAmount'] ?? 0);

            if ($type === AlliancePayment::OPERATION_TYPE_REFUND) {
                if (in_array($status, self::REFUND_COUNTED_STATUSES, true)) {
                    $refunded += $coins;
                }
                continue;
            }

            if ($status !== AllianceOrderInterface::ORDER_STATUS_SUCCESS) {
                continue;
            }

            if ($type === AlliancePayment::OPERATION_TYPE_COMPLETION) {
                $completion += $coins;
            } elseif ($coins > 0
                && ($type === AlliancePayment::OPERATION_TYPE_PURCHASE
                    || $type === AlliancePayment::OPERATION_TYPE_PREAUTH)
            ) {
                $captured = $coins;
            }
        }

        return [$completion > 0 ? $completion : $captured, $refunded];
    }

    /**
     * @param float $amount
     * @return int
     */
    private function toCoins(float $amount): int
    {
        return (int)round($amount * 100);
    }
}
