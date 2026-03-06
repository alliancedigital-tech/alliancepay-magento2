<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Processor;

use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Exception\CallbackException;
use Alliance\AlliancePay\Exception\GatewayException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Payment\CallbackService;

/**
 * Class PaymentStatusCheckProcessor.
 */
class PaymentStatusProcessor
{
    public function __construct(
        private readonly GatewayClientInterface $gatewayClient,
        private readonly CallbackService $callbackService,
        private readonly Logger $logger
    ) {}

    /**
     * @param $hppOrderId
     * @return array
     */
    public function process($hppOrderId): array
    {
        try {
            $callbackData = $this->gatewayClient->getOperationStatus($hppOrderId);

            return $this->callbackService->processCallback($callbackData);
        } catch (GatewayException|CallbackException $exception) {
            $this->logger->error($exception->getMessage());

            return ['status' => 'failed'];
        }
    }
}
