<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Processor;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Exception\CallbackException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Payment\CallbackService;

/**
 * Class CallbackProcessor.
 */
class CallbackProcessor
{
    public function __construct(
        private CallbackService $callbackService,
        private Logger $logger
    ) {}

    /**
     * @param array $callbackData
     * @return void
     */
    public function processCallback(array $callbackData): void
    {
        try {
            $status = $this->callbackService->processCallback($callbackData);

            if ($status[AllianceOrderInterface::ORDER_STATUS] === AllianceOrderInterface::ORDER_STATUS_FAIL) {
                $this->logger->error($status['message']);
            }
        } catch (CallbackException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
