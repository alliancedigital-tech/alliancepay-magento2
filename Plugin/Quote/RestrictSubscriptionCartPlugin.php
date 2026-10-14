<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Plugin\Quote;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;

/**
 * RestrictSubscriptionCartPlugin.
 */
class RestrictSubscriptionCartPlugin
{
    public function __construct(
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository
    ) {
    }

    /**
     * @param Quote $subject
     * @param callable $proceed
     * @param mixed $product
     * @param mixed $request
     * @param mixed $processMode
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundAddProduct(
        Quote $subject,
        callable $proceed,
        $product,
        $request = null,
        $processMode = null
    ) {
        $requestData = $request instanceof DataObject ? $request->getData() : (array)$request;
        $wantsSubscription = ($requestData['alliance_purchase_mode'] ?? 'onetime') === 'subscribe';

        if ($wantsSubscription) {
            $plan = $this->resolveRequestedPlan((int)($requestData['alliance_plan_id'] ?? 0), $product);

            if (!$plan) {
                throw new LocalizedException(__('Please select a valid subscription plan.'));
            }

            if ($subject->getCustomerIsGuest()) {
                throw new LocalizedException(
                    __('Please log in to your account to subscribe to this product.')
                );
            }
        }

        $hasOtherItems = false;
        $hasSubscriptionItem = false;

        foreach ($subject->getAllVisibleItems() as $item) {
            $itemIsSubscription = $item->getBuyRequest()->getData('alliance_purchase_mode') === 'subscribe';
            $isSameProductSameMode = (int)$item->getProductId() === (int)$product->getId()
                && $itemIsSubscription === $wantsSubscription;

            if ($isSameProductSameMode) {
                continue;
            }

            $hasOtherItems = true;

            if ($itemIsSubscription) {
                $hasSubscriptionItem = true;
            }
        }

        if ($wantsSubscription && $hasOtherItems) {
            throw new LocalizedException(
                __('A subscription cannot be combined with other items in your cart. Please complete or clear your cart first.')
            );
        }

        if (!$wantsSubscription && $hasSubscriptionItem) {
            throw new LocalizedException(
                __('A subscription cannot be combined with other items in your cart. Please complete or clear your cart first.')
            );
        }

        return $proceed($product, $request, $processMode);
    }

    /**
     * @param int $planId
     * @param ProductInterface|DataObject $product
     * @return SubscriptionPlanInterface|null
     */
    private function resolveRequestedPlan(int $planId, ProductInterface|DataObject $product): ?SubscriptionPlanInterface
    {
        if (!$planId) {
            return null;
        }

        try {
            $plan = $this->subscriptionPlanRepository->get($planId);
        } catch (NoSuchEntityException) {
            return null;
        }

        return $plan->isActive() && (int)$plan->getProductId() === (int)$product->getId() ? $plan : null;
    }
}
