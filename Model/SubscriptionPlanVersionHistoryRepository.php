<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanVersionHistoryInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlanVersionHistoryResource;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * SubscriptionPlanVersionHistoryRepository.
 */
class SubscriptionPlanVersionHistoryRepository
{
    public function __construct(
        private readonly SubscriptionPlanVersionHistoryResource $resource,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param SubscriptionPlanVersionHistoryInterface $entity
     * @return SubscriptionPlanVersionHistoryInterface
     * @throws CouldNotSaveException
     */
    public function save(SubscriptionPlanVersionHistoryInterface $entity): SubscriptionPlanVersionHistoryInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save subscription plan version history: %1', $e->getMessage()));
        }

        return $entity;
    }
}
