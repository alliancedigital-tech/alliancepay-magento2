<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Block\Product\View;

use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * PurchaseOptions.
 */
class PurchaseOptions extends Template
{
    public function __construct(
        Context $context,
        private readonly Registry $coreRegistry,
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * @return bool
     */
    public function hasSubscriptionOption(): bool
    {
        return !empty($this->getSubscriptionPlans());
    }

    /**
     * @return array
     */
    public function getSubscriptionPlans(): array
    {
        $product = $this->getProduct();

        if (!$product) {
            return [];
        }

        return $this->subscriptionPlanRepository->getActiveListByProductId((int)$product->getId());
    }
}
