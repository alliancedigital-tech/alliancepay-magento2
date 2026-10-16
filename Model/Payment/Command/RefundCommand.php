<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Command;

use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Model\Payment\Processor\RefundProcessor;
use Alliance\AlliancePay\Model\Payment\Validator\RefundRestrictionValidator;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;

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
        private readonly RefundAmountResolver $refundAmountResolver
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
        $order = $payment->getOrder();
        $orderCurrencyAmount = $this->resolveOrderCurrencyAmount($payment, (float)$amount);
        $refundCoinAmount = $this->refundAmountResolver->resolve(
            $allianceOrder,
            $orderCurrencyAmount,
            (float)$order->getGrandTotal(),
            $this->resolveAlreadyRefunded($payment, $orderCurrencyAmount)
        );

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

        $operations = $this->refundAmountResolver->getOperations($allianceOrder);

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
     * @param Payment $payment
     * @param float $orderCurrencyAmount
     * @return float
     */
    private function resolveAlreadyRefunded(Payment $payment, float $orderCurrencyAmount): float
    {
        $totalRefunded = (float)$payment->getOrder()->getTotalRefunded();

        return $payment->getCreditmemo() ? max(0.0, $totalRefunded - $orderCurrencyAmount) : $totalRefunded;
    }

    /**
     * @param Payment $payment
     * @param float $baseAmount
     * @return float
     */
    private function resolveOrderCurrencyAmount(Payment $payment, float $baseAmount): float
    {
        $creditmemo = $payment->getCreditmemo();

        return $creditmemo ? (float)$creditmemo->getGrandTotal() : $baseAmount;
    }
}
