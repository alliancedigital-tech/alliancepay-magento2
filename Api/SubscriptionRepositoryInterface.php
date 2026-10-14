<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

use Alliance\AlliancePay\Api\Data\SubscriptionInterface;

/**
 * SubscriptionRepositoryInterface.
 */
interface SubscriptionRepositoryInterface
{
    /**
     * @param int $id
     * @return SubscriptionInterface
     */
    public function get(int $id): SubscriptionInterface;

    /**
     * @param string $providerOrderId
     * @return SubscriptionInterface
     */
    public function getByProviderOrderId(string $providerOrderId): SubscriptionInterface;

    /**
     * @param SubscriptionInterface $entity
     * @return bool
     */
    public function delete(SubscriptionInterface $entity): bool;

    /**
     * @param SubscriptionInterface $entity
     * @return SubscriptionInterface
     */
    public function save(SubscriptionInterface $entity): SubscriptionInterface;
}
