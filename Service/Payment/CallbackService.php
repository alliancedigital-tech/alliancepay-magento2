<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Payment;

use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Exception\CallbackException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Service\ConvertData\ConvertDataService;
use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Service\InvoiceService;

/**
 * Class CallbackService.
 */
class CallbackService
{
    public function __construct(
        private AllianceOrderRepositoryInterface $allianceOrderRepository,
        private AllianceConfig $allianceConfig,
        private OrderRepositoryInterface $orderRepository,
        private readonly ConvertDataService $convertDataService,
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly InvoiceService $invoiceService,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private CreditmemoManagementInterface $creditmemoManagement,
        private Logger $logger
    ) {}

    /**
     * @param array $callbackData
     * @return array
     * @throws CallbackException
     */
    public function processCallback(array $callbackData): array
    {
        try {
            $this->validateSignature($callbackData);

            if ($this->allianceConfig->isLogsEnabled()) {
                $this->logger->info(
                    'Callback request data: ' . print_r($callbackData, true)
                );
            }

            $convertedCallback = $this->convertDataService->camelToSnakeArrayKeys($callbackData);

            if ($convertedCallback[AllianceOrderInterface::HPP_ORDER_ID]) {
                $allianceOrder = $this->allianceOrderRepository->getByHppOrderId(
                    $convertedCallback[AllianceOrderInterface::HPP_ORDER_ID]
                );
                $orderId = $allianceOrder->getOrderId();
                $order = $this->orderRepository->get($orderId);
                $allianceOrder->setCallbackData($callbackData);
                $allianceOrder->setIsCallbackReturned(true);
                $this->updateOrderByCallback(
                    $order,
                    $allianceOrder,
                    $convertedCallback
                );
            }
        } catch (Exception $e) {
            throw new CallbackException(__('Callback processing failed: %1', $e->getMessage()));
        }

        return [
            AllianceOrderInterface::ORDER_STATUS => $allianceOrder->getOrderStatus()
        ];
    }

    /**
     * @param array $callbackData
     * @return void
     */
    private function validateSignature(array $callbackData): void
    {
    }

    /**
     * @param OrderInterface $order
     * @param AllianceOrderInterface $allianceOrder
     * @param array $callbackData
     * @return void
     * @throws LocalizedException
     */
    private function updateOrderByCallback(
        OrderInterface $order,
        AllianceOrderInterface $allianceOrder,
        array $callbackData
    ): void {
        $status = $callbackData[AllianceOrderInterface::ORDER_STATUS] ?? AllianceOrderInterface::ORDER_STATUS_PENDING;

        match ($status) {
            AllianceOrderInterface::ORDER_STATUS_SUCCESS => $this->markOrderAsSuccessful($order, $callbackData),
            AllianceOrderInterface::ORDER_STATUS_FAIL => $this->markOrderAsFailed($order),
            default => $this->logger->warning('Unknown callback status')
        };

        $this->dataObjectHelper->populateWithArray(
            $allianceOrder,
            $callbackData,
            AllianceOrderInterface::class
        );

        $this->allianceOrderRepository->save($allianceOrder);
        $this->updateRefunds($order, $callbackData);
    }

    public function updateRefunds(OrderInterface $order, array $callbackData): void
    {
        if (!$order->hasCreditmemos()) {
            return;
        }

        $operationIds = [];
        if (isset($callbackData['operations'])) {
            $operations = $callbackData['operations'];
        } else {
            $operations = isset($callbackData['operation']) ? [$callbackData['operation']] : [];
        }

        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            if ($creditmemo->getState() == Creditmemo::STATE_OPEN && !empty($creditmemo->getTransactionId())) {
                foreach ($operations as $operation) {
                    $operationIds[] = $operation[RefundService::REFUND_DATA_FIELD_OPERATION_ID] ?? '';
                }
            }
        }

        if (in_array($creditmemo->getTransactionId(), $operationIds)) {
            $this->creditmemoManagement->refund($creditmemo);
        }
    }

    /**
     * @param OrderInterface $order
     * @param array $callbackData
     * @return void
     * @throws LocalizedException
     */
    private function markOrderAsSuccessful(OrderInterface $order, array $callbackData): void
    {
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);

            if ($invoice) {
                $operationId = $this->getOperationId($callbackData);
                $invoice->register();
                $invoice->pay();
                $invoice->setTransactionId($operationId);
                $this->invoiceRepository->save($invoice);
                $order->setState($this->allianceConfig->getSuccessOrderStatus());
                $order->setStatus($this->allianceConfig->getSuccessOrderStatus());
                $order->addCommentToStatusHistory(__('Payment successfully completed by bank'));
                $order->getPayment()->setAdditionalInformation(
                    AllianceOrderInterface::OPERATION_ID,
                    $operationId
                );
                $order->setIsInProcess(true);
                $this->orderRepository->save($order);
                $order->addCommentToStatusHistory(__('Invoice successfully created.'));
            }
        }
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    private function markOrderAsFailed(OrderInterface $order): void
    {
        $order->setState($this->allianceConfig->getFailedOrderStatus());
        $order->setStatus($this->allianceConfig->getFailedOrderStatus());
        $order->addCommentToStatusHistory('Payment failed.');
        $this->orderRepository->save($order);
    }

    /**
     * @param array $callbackData
     * @return string
     */
    private function getOperationId(array $callbackData): string
    {
        if (isset($callbackData['operation'])) {
            $convertedOperation = $this->convertDataService->camelToSnakeArrayKeys($callbackData['operation']);
            if (isset($convertedOperation['type'])
                && $convertedOperation['type'] === AlliancePayment::OPERATION_TYPE
                && isset($convertedOperation[AllianceOrderInterface::OPERATION_STATUS])
                && $convertedOperation[AllianceOrderInterface::OPERATION_STATUS]
                    === AllianceOrderInterface::ORDER_STATUS_SUCCESS
            ) {

            }

            return $convertedOperation[AllianceOrderInterface::OPERATION_ID];
        } elseif (isset($callbackData['operations']) && is_iterable($callbackData['operations'])) {
            foreach ($callbackData['operations'] as $operation) {
                $convertedOperation = $this->convertDataService->camelToSnakeArrayKeys($operation);
                if (isset($convertedOperation[AllianceOrderInterface::OPERATION_ID])
                    && isset($convertedOperation['type'])
                    && $convertedOperation['type'] === AlliancePayment::OPERATION_TYPE
                    && isset($convertedOperation[AllianceOrderInterface::OPERATION_STATUS])
                    && $convertedOperation[AllianceOrderInterface::OPERATION_STATUS]
                        === AllianceOrderInterface::ORDER_STATUS_SUCCESS
                ) {
                    return $convertedOperation[AllianceOrderInterface::OPERATION_ID];
                }
            }
        }

        return '';
    }
}
