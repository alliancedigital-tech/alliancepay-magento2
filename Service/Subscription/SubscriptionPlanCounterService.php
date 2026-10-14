<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;

/**
 * SubscriptionPlanCounterService.
 */
class SubscriptionPlanCounterService
{
    public function __construct(
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository
    ) {
    }

    /**
     * @param int $planId
     * @return void
     */
    public function increment(int $planId): void
    {
        $plan = $this->subscriptionPlanRepository->get($planId);
        $plan->setActiveSubscriptionsCount($plan->getActiveSubscriptionsCount() + 1);
        $this->subscriptionPlanRepository->save($plan);
    }

    /**
     * @param int $planId
     * @return void
     */
    public function decrement(int $planId): void
    {
        $plan = $this->subscriptionPlanRepository->get($planId);
        $plan->setActiveSubscriptionsCount(max(0, $plan->getActiveSubscriptionsCount() - 1));
        $this->subscriptionPlanRepository->save($plan);
    }
}
