<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionAuditLogInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionAuditLogResource;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * SubscriptionAuditLogRepository.
 */
class SubscriptionAuditLogRepository
{
    public function __construct(
        private readonly SubscriptionAuditLogResource $resource,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param SubscriptionAuditLogInterface $entity
     * @return SubscriptionAuditLogInterface
     * @throws CouldNotSaveException
     */
    public function save(SubscriptionAuditLogInterface $entity): SubscriptionAuditLogInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save subscription audit log: %1', $e->getMessage()));
        }

        return $entity;
    }
}
