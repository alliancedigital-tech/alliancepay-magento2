<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Payment;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Service\Payment\Service\ServiceAbstract;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class PreAuthCaptureService.
 */
class PreAuthCaptureService extends ServiceAbstract
{
    public function __construct(
        private readonly GatewayClientInterface $gatewayClient,
        private readonly AllianceConfig $allianceConfig,
        private readonly AlliancePayment $alliancePayment,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SerializerInterface $serializer,
        private readonly TimezoneInterface $timezone,
        private readonly Logger $logger
    ) {}

    /**
     * @param OrderInterface $order
     * @param AllianceOrderInterface $allianceOrder
     * @return string
     * @throws LocalizedException
     */
    public function execute(OrderInterface $order, AllianceOrderInterface $allianceOrder): string
    {
        $originalOperationId = $this->resolveOriginalOperationId($allianceOrder);

        if (empty($originalOperationId)) {
            throw new LocalizedException(__(
                'Cannot capture PREAUTH order #%1: bank callback has not been received yet. '
                . 'Please use "Check Payment Status" to refresh the order status and try again.',
                $order->getIncrementId()
            ));
        }

        $this->validateAmount($order, $allianceOrder);

        $coinAmount = $this->prepareCoinAmount((float)$order->getGrandTotal());
        $now = $this->timezone->date();

        $payload = [
            'merchantRequestId' => $this->generateMerchantRequestId(),
            'merchantId' => $this->allianceConfig->getMerchantId(),
            'originalOperationId' => $originalOperationId,
            'coinAmount' => (string)$coinAmount,
            'date' => $now->format('Y-m-d H:i:s.00P'),
            'notificationUrl' => $this->alliancePayment->getCallbackUrl(),
        ];

        if ($this->allianceConfig->isLogsEnabled()) {
            $this->logger->info(
                'PreAuth completion request for order #' . $order->getIncrementId()
                . ': ' . print_r($payload, true)
            );
        }

        $response = $this->gatewayClient->completion($payload);

        if ($this->allianceConfig->isLogsEnabled()) {
            $this->logger->info(
                'PreAuth completion request for order #' . $order->getIncrementId(),
                $payload
            );
        }

        if (!empty($response['success']) && $response['success'] === false) {
            $message = $response['message'] ?? __('Unknown gateway error');
            $this->logger->error(
                'PreAuth completion failed for order #' . $order->getIncrementId() . ': ' . $message
            );
            throw new LocalizedException(__('PREAUTH capture failed: %1', $message));
        }

        $completionOperationId = $response['operationId'] ?? $originalOperationId;

        $captureStatus = $this->allianceConfig->getPreAuthCaptureSuccessStatus();
        if ($captureStatus) {
            $order->setStatus($captureStatus);
            $order->addCommentToStatusHistory(
                __('PREAUTH capture completed successfully. Operation ID: %1', $completionOperationId)
            );
            $this->orderRepository->save($order);
        }

        return $completionOperationId;
    }

    /**
     * @param AllianceOrderInterface $allianceOrder
     * @return string
     */
    private function resolveOriginalOperationId(AllianceOrderInterface $allianceOrder): string
    {
        $callbackData = $allianceOrder->getCallbackData();
        if (empty($callbackData)) {
            return '';
        }

        try {
            $data = $this->serializer->unserialize($callbackData);
        } catch (Exception $e) {
            $this->logger->error('Failed to unserialize callback_data: ' . $e->getMessage());
            return '';
        }

        $operations = [];
        if (isset($data['operations']) && is_array($data['operations'])) {
            $operations = $data['operations'];
        } elseif (isset($data['operation']) && is_array($data['operation'])) {
            $operations = [$data['operation']];
        }

        foreach ($operations as $operation) {
            $type = $operation['type'] ?? '';
            $status = $operation['status'] ?? '';

            if ($type === AlliancePayment::HPP_PAY_TYPE_PREAUTH
                && $status === AllianceOrderInterface::ORDER_STATUS_SUCCESS
            ) {
                return $operation['operationId'] ?? '';
            }
        }

        return '';
    }

    /**
     * @param OrderInterface $order
     * @param AllianceOrderInterface $allianceOrder
     * @return void
     * @throws LocalizedException
     */
    private function validateAmount(OrderInterface $order, AllianceOrderInterface $allianceOrder): void
    {
        $originalAmount = $allianceOrder->getOriginalAuthorizedAmount();

        if ($originalAmount === null || $originalAmount <= 0) {
            throw new LocalizedException(__(
                'Cannot capture order #%1: original authorized amount is not recorded.',
                $order->getIncrementId()
            ));
        }

        $currentCoinAmount = $this->prepareCoinAmount((float)$order->getGrandTotal());
        $diffPercent = abs($currentCoinAmount - $originalAmount) / $originalAmount * 100;

        if ($diffPercent > 20) {
            throw new LocalizedException(__(
                'Cannot capture order #%1: current amount (%2 coins) differs from the original authorized amount '
                . '(%3 coins) by %4%, which exceeds the allowed ±20% threshold.',
                $order->getIncrementId(),
                $currentCoinAmount,
                $originalAmount,
                round($diffPercent, 2)
            ));
        }
    }

}
