<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
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
use Magento\Framework\Serialize\SerializerInterface;
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
        private readonly SerializerInterface $serializer,
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
                $previousCallbackData = $allianceOrder->getCallbackData();
                $allianceOrder->setCallbackData($callbackData);
                $allianceOrder->setIsCallbackReturned(true);
                $this->updateOrderByCallback(
                    $order,
                    $allianceOrder,
                    $convertedCallback,
                    $callbackData,
                    $previousCallbackData
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
     * @param array $callbackData snake_case converted payload
     * @param array $rawCallbackData Original camelCase payload
     * @param string|null $previousCallbackData JSON string of previously saved callback_data
     * @return void
     * @throws LocalizedException
     */
    private function updateOrderByCallback(
        OrderInterface $order,
        AllianceOrderInterface $allianceOrder,
        array $callbackData,
        array $rawCallbackData,
        ?string $previousCallbackData
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

        $this->updateOperationsInCallbackData(
            $allianceOrder,
            $rawCallbackData,
            $previousCallbackData
        );

        $allianceOrder->setTransactionType(
            $this->getTransactionTypeFromPaymentOperation(
                $this->getOperationsFromCallbackData($callbackData)
            )
        );

        $this->allianceOrderRepository->save($allianceOrder);
        $this->updateRefunds($order, $callbackData);
    }

    /**
     * @param OrderInterface $order
     * @param array $callbackData
     * @return void
     */
    public function updateRefunds(OrderInterface $order, array $callbackData): void
    {
        if (!$order->hasCreditmemos()) {
            return;
        }

        $operations = $this->getOperationsFromCallbackData($callbackData);
        $operationIds = array_column($operations, RefundService::REFUND_DATA_FIELD_OPERATION_ID);

        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            if ($creditmemo->getState() == Creditmemo::STATE_OPEN
                && !empty($creditmemo->getTransactionId())
                && in_array($creditmemo->getTransactionId(), $operationIds)
            ) {
                $this->creditmemoManagement->refund($creditmemo);
            }
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
     * @param AllianceOrderInterface $allianceOrder
     * @param array $rawCallbackData
     * @param string|null $previousCallbackData
     * @return void
     */
    private function updateOperationsInCallbackData(
        AllianceOrderInterface $allianceOrder,
        array $rawCallbackData,
        ?string $previousCallbackData
    ): void {
        $newOperations = $this->getOperationsFromCallbackData($rawCallbackData);

        if (empty($newOperations)) {
            return;
        }

        $storedData = $previousCallbackData !== null
            ? (array) $this->serializer->unserialize($previousCallbackData)
            : [];

        $existingOperations = (array) ($storedData['operations'] ?? []);
        if (empty($existingOperations) && isset($storedData['operation'])) {
            $existingOperations = [$storedData['operation']];
        }

        $existingIds = array_column($existingOperations, 'operationId');

        foreach ($newOperations as $operation) {
            $operationId = $operation['operationId'] ?? null;

            if ($operationId === null || $operationId === '') {
                $existingOperations[] = $operation;
                continue;
            }

            if (!in_array($operationId, $existingIds, strict: true)) {
                $existingOperations[] = $operation;
                $existingIds[]        = $operationId;
            }
        }

        $currentCallbackData = (array) $this->serializer->unserialize(
            $allianceOrder->getCallbackData() ?? '[]'
        );

        $currentCallbackData['operations'] = $existingOperations;
        unset($currentCallbackData['operation']);

        $allianceOrder->setCallbackData($currentCallbackData);
    }

    /**
     * @param array $callbackData
     * @return string
     */
    private function getOperationId(array $callbackData): string
    {
        $operations = $this->getOperationsFromCallbackData($callbackData);

        foreach ($operations as $operation) {
            $convertedOperation = $this->convertDataService->camelToSnakeArrayKeys($operation);

            if ($this->isSuccessfulPurchaseOperation($convertedOperation)) {
                return $convertedOperation[AllianceOrderInterface::OPERATION_ID];
            }
        }

        return '';
    }

    /**
     * @param array $operation
     * @return bool
     */
    private function isSuccessfulPurchaseOperation(array $operation): bool
    {
        return isset(
                $operation[AllianceOrderInterface::OPERATION_ID],
                $operation['type'],
                $operation[AllianceOrderInterface::OPERATION_STATUS]
            )
            && in_array(
                $operation['type'],
                [
                    AlliancePayment::OPERATION_TYPE_PURCHASE,
                    AlliancePayment::OPERATION_TYPE_A2A
                ]
            )
            && $operation[AllianceOrderInterface::OPERATION_STATUS] === AllianceOrderInterface::ORDER_STATUS_SUCCESS;
    }

    /**
     * @param array $operations
     * @return int|null
     */
    private function getTransactionTypeFromPaymentOperation(array $operations): ?int
    {
        foreach ($operations as $operation) {
            $operationConverted = $this->convertDataService->camelToSnakeArrayKeys($operation);
            if (!empty($operationConverted[AllianceOrderInterface::OPERATION_TYPE])
                && !empty($operationConverted[AllianceOrderInterface::TRANSACTION_TYPE])
                && (
                    $operationConverted[AllianceOrderInterface::OPERATION_TYPE] === AlliancePayment::OPERATION_TYPE_PURCHASE
                    || $operationConverted[AllianceOrderInterface::OPERATION_TYPE] === AlliancePayment::OPERATION_TYPE_A2A
                )
            ) {
                return $operationConverted[AllianceOrderInterface::TRANSACTION_TYPE];
            }
        }

        return null;
    }

    /**
     * @param array $callbackData
     * @return array
     */
    private function getOperationsFromCallbackData(array $callbackData): array
    {
        return match (true) {
            isset($callbackData['operations']) => (array) $callbackData['operations'],
            isset($callbackData['operation']) => [$callbackData['operation']],
            default => [],
        };
    }
}
