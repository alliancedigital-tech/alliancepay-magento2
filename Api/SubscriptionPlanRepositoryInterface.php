<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;

/**
 * SubscriptionPlanRepositoryInterface.
 */
interface SubscriptionPlanRepositoryInterface
{
    /**
     * @param int $id
     * @return SubscriptionPlanInterface
     */
    public function get(int $id): SubscriptionPlanInterface;

    /**
     * @param string $planKey
     * @return SubscriptionPlanInterface
     */
    public function getByPlanKey(string $planKey): SubscriptionPlanInterface;

    /**
     * @param string $providerPlanId
     * @return SubscriptionPlanInterface
     */
    public function getByProviderPlanId(string $providerPlanId): SubscriptionPlanInterface;

    /**
     * @param int $productId
     * @return SubscriptionPlanInterface
     */
    public function getActiveByProductId(int $productId): SubscriptionPlanInterface;

    /**
     * @param int $productId
     * @return array
     */
    public function getActiveListByProductId(int $productId): array;

    /**
     * @param SubscriptionPlanInterface $entity
     * @return bool
     */
    public function delete(SubscriptionPlanInterface $entity): bool;

    /**
     * @param SubscriptionPlanInterface $entity
     * @return SubscriptionPlanInterface
     */
    public function save(SubscriptionPlanInterface $entity): SubscriptionPlanInterface;
}
