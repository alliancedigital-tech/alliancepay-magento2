<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Command;

use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Model\Payment\Processor\RefundProcessor;
use Alliance\AlliancePay\Model\Payment\Validator\RefundRestrictionValidator;
use Exception;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class RefundCommand.
 */
class RefundCommand implements CommandInterface
{
    public function __construct(
        private readonly RefundProcessor $refundProcessor,
        private readonly AlliancePayment $alliancePayment,
        private readonly AllianceOrderRepositoryInterface $allianceOrderRepository,
        private readonly RefundRestrictionValidator $refundRestrictionValidator,
        private readonly SerializerInterface $serializer,
        private readonly Logger $logger
    ) {}

    /**
     * @param array $commandSubject
     * @return void
     * @throws CommandException
     */
    public function execute(array $commandSubject): void
    {
        $paymentDO = SubjectReader::readPayment($commandSubject);
        $amount = SubjectReader::readAmount($commandSubject);
        $payment = $paymentDO->getPayment();
        $operationId = $payment->getAdditionalInformation(AllianceOrderInterface::OPERATION_ID);

        if (!$operationId) {
            throw new CommandException(__('Operation ID not found. Cannot process refund.'));
        }

        $allianceOrder = $this->allianceOrderRepository->getByOrderId(
            (string)$payment->getOrder()->getId()
        );

        if ($this->refundRestrictionValidator->isRefundRestricted($allianceOrder)) {
            throw new CommandException(
                __('Online refund is not available for this transaction type.')
            );
        }
        $resolvedOperationId = $this->resolveForRefundOperationId($allianceOrder, $operationId);
        $refundCoinAmount = $this->resolveRefundCoinAmount($allianceOrder, (float)$amount);

        $result = $this->refundProcessor->processRefund(
            $resolvedOperationId,
            $refundCoinAmount,
            $this->alliancePayment->getCallbackUrl()
        );

        if (isset($result['success']) && $result['success'] === false) {
            throw new CommandException(__('Refund failed: %1', $result['message'] ?? ''));
        }
    }

    /**
     * @param AllianceOrderInterface $allianceOrder
     * @param string $fallbackOperationId
     * @return string
     */
    private function resolveForRefundOperationId(
        AllianceOrderInterface $allianceOrder,
        string $fallbackOperationId
    ): string {
        if ($allianceOrder->getHppPayType() !== AlliancePayment::HPP_PAY_TYPE_PREAUTH) {
            return $fallbackOperationId;
        }

        $callbackData = $allianceOrder->getCallbackData();
        if (empty($callbackData)) {
            return $fallbackOperationId;
        }

        try {
            $data = $this->serializer->unserialize($callbackData);
        } catch (Exception $e) {
            $this->logger->error('Failed to unserialize callback_data: ' . $e->getMessage());

            return $fallbackOperationId;
        }

        $operations = $data['operations'] ?? [];
        if (empty($operations) && isset($data['operation'])) {
            $operations = [$data['operation']];
        }

        foreach ($operations as $operation) {
            if (($operation['type'] ?? '') === AlliancePayment::OPERATION_TYPE_COMPLETION
                && ($operation['status'] ?? '') === AllianceOrderInterface::ORDER_STATUS_SUCCESS
            ) {
                return $operation['operationId'] ?? $fallbackOperationId;
            }
        }

        return $fallbackOperationId;
    }

    /**
     * @param AllianceOrderInterface $allianceOrder
     * @param float $magentoRefundAmount
     * @return float
     */
    private function resolveRefundCoinAmount(
        AllianceOrderInterface $allianceOrder,
        float $magentoRefundAmount
    ): float {
        $conversionRate = $allianceOrder->getConversionRate();

        if ($conversionRate === null || $conversionRate <= 0) {
            return $magentoRefundAmount;
        }

        $originalCoinAmount = (int)$allianceOrder->getCoinAmount();
        $refundCoinAmountConverted = (int) round($magentoRefundAmount * $conversionRate * 100);

        if ($refundCoinAmountConverted === $originalCoinAmount) {
            return (float)($originalCoinAmount / 100);
        }

        return $magentoRefundAmount * $conversionRate;
    }
}
