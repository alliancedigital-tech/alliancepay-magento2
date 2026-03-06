<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Plugin;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Model\Payment\Processor\RefundProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Service\CreditmemoService;

/**
 * Class CreateOpenRefund.
 */
class CreateOpenRefund
{
    public function __construct(
        private CreditMemoRepositoryInterface $creditMemoRepository,
        private RefundProcessor $refundProcessor,
        private AlliancePayment $alliancePayment,
    ) {}

    /**
     * @param CreditmemoService $subject
     * @param callable $proceed
     * @param CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return CreditmemoInterface
     */
    public function aroundRefund(
        CreditmemoService $subject,
        callable $proceed,
        CreditmemoInterface $creditmemo,
        $offlineRequested = false
    ): CreditmemoInterface {
        if ($this->canCreateDraftRefund($creditmemo)) {
            $order = $creditmemo->getOrder();

            if ($order->getPayment()->getMethod() !== AlliancePayment::PAYMENT_METHOD_CODE) {
                throw new LocalizedException(__('The payment method is not available.'));
            }

            $operationId = $order->getPayment()->getAdditionalInformation(AllianceOrderInterface::OPERATION_ID);

            if (!$operationId) {
                throw new LocalizedException(__('Operation id not specified.'));
            }

            $response = $this->refundProcessor->processRefund(
                $operationId,
                $creditmemo->getGrandTotal(),
                $this->alliancePayment->getCallbackUrl()
            );

            if ($response['success'] && !empty($response['transaction_id'])) {
                $creditmemo->setTransactionId($response['transaction_id']);
            }
            $creditmemo->setState(Creditmemo::STATE_OPEN);
            $this->creditMemoRepository->save($creditmemo);

            return $creditmemo;
        }

        return $proceed($creditmemo, $offlineRequested);
    }

    /**
     * @param CreditmemoInterface $creditmemo
     * @return bool
     */
    private function canCreateDraftRefund(CreditmemoInterface $creditmemo): bool
    {
        $creditMemoTransactionId = $creditmemo->getTransactionId();

        return empty($creditMemoTransactionId);
    }
}
