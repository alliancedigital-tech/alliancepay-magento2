<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Processor;

use Alliance\AlliancePay\Api\PaymentProcessorInterface;
use Alliance\AlliancePay\Exception\PaymentException;
use Alliance\AlliancePay\Service\Payment\CreateOrderService;
use Alliance\AlliancePay\Exception\GatewayException;
use Alliance\AlliancePay\Logger\Logger;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class PaymentProcessor.
 */
class PaymentProcessor implements PaymentProcessorInterface
{
    public function __construct(
        private readonly CreateOrderService $createOrderService,
        private readonly Logger $logger
    ) {}

    /**
     * @inheritDoc
     */
    public function process(OrderInterface $order): array
    {
        $redirectData = [];

        if ($order->getId()) {
            try {
                $redirectData = $this->createOrderService->createOrder($order);
            } catch (GatewayException $exception) {
                $this->logger->error($exception->getMessage());
            } catch (PaymentException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $redirectData;
    }
}
