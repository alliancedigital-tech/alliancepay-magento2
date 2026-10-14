<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Processor;

use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Subscription\SubscriptionCallbackService;
use Throwable;

/**
 * SubscriptionCallbackProcessor.
 */
class SubscriptionCallbackProcessor
{
    public function __construct(
        private readonly SubscriptionCallbackService $subscriptionCallbackService,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param array $callbackData
     * @return void
     */
    public function processCallback(array $callbackData): void
    {
        try {
            $this->subscriptionCallbackService->processCallback($callbackData);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
