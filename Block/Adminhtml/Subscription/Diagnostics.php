<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Block\Adminhtml\Subscription;

use Alliance\AlliancePay\Api\Data\SubscriptionInterface;
use Alliance\AlliancePay\Service\Subscription\DiagnosticsService;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * Diagnostics.
 */
class Diagnostics extends Template
{
    private array $freshOperations;

    public function __construct(
        Context $context,
        private readonly Registry $coreRegistry,
        private readonly DiagnosticsService $diagnosticsService,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription(): SubscriptionInterface
    {
        return $this->coreRegistry->registry('alliance_subscription');
    }

    /**
     * @return array
     */
    public function getFreshOperations(): array
    {
        if (!isset($this->freshOperations)) {
            $this->freshOperations = $this->diagnosticsService->getFreshOperations(
                (string)$this->getSubscription()->getProviderOrderId()
            );
        }

        return $this->freshOperations;
    }

    /**
     * @return bool
     */
    public function hasProviderError(): bool
    {
        $operations = $this->getFreshOperations();

        return isset($operations['success']) && $operations['success'] === false;
    }
}
