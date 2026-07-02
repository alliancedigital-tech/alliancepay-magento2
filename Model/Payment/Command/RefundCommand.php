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

/**
 * Class RefundCommand.
 */
class RefundCommand implements CommandInterface
{
    public function __construct(
        private readonly RefundProcessor $refundProcessor,
        private readonly AlliancePayment $alliancePayment,
        private readonly AllianceOrderRepositoryInterface $allianceOrderRepository,
        private readonly RefundRestrictionValidator $refundRestrictionValidator
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

        $result = $this->refundProcessor->processRefund(
            $operationId,
            (float)$amount,
            $this->alliancePayment->getCallbackUrl()
        );

        if (isset($result['success']) && $result['success'] === false) {
            throw new CommandException(__('Refund failed: %1', $result['message'] ?? ''));
        }
    }
}
