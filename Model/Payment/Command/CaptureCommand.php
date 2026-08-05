<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Command;

use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Service\Payment\PreAuthCaptureService;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class CaptureCommand.
 */
class CaptureCommand implements CommandInterface
{
    public function __construct(
        private readonly PreAuthCaptureService $preAuthCaptureService,
        private readonly AllianceOrderRepositoryInterface $allianceOrderRepository
    ) {}

    /**
     * @param array $commandSubject
     * @return void
     * @throws LocalizedException
     */
    public function execute(array $commandSubject): void
    {
        $paymentDataObject = SubjectReader::readPayment($commandSubject);
        $order = $paymentDataObject->getPayment()->getOrder();

        try {
            $allianceOrder = $this->allianceOrderRepository->getByOrderId((string) $order->getId());
        } catch (NoSuchEntityException $e) {
            return;
        }

        if ($allianceOrder->getHppPayType() !== AlliancePayment::HPP_PAY_TYPE_PREAUTH) {
            return;
        }

        $completionOperationId = $this->preAuthCaptureService->execute($order, $allianceOrder);

        if ($completionOperationId) {
            $payment = $paymentDataObject->getPayment();
            $payment->setTransactionId($completionOperationId);
            $payment->setIsTransactionClosed(false);
            $payment->setAdditionalInformation(
                AllianceOrderInterface::OPERATION_ID,
                $completionOperationId
            );
        }
    }
}
