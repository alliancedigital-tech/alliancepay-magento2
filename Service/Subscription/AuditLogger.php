<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\Data\SubscriptionAuditLogInterfaceFactory;
use Alliance\AlliancePay\Model\SubscriptionAuditLogRepository;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * AuditLogger.
 */
class AuditLogger
{
    public function __construct(
        private readonly SubscriptionAuditLogInterfaceFactory $auditLogFactory,
        private readonly SubscriptionAuditLogRepository $auditLogRepository,
        private readonly DateTime $dateTime
    ) {
    }

    /**
     * @param string $action
     * @param int|null $planId
     * @param int|null $productId
     * @param int|null $userId
     * @param array $details
     * @return void
     */
    public function log(string $action, ?int $planId, ?int $productId, ?int $userId, array $details = []): void
    {
        $entry = $this->auditLogFactory->create();
        $entry->setAction($action);
        $entry->setPlanId($planId);
        $entry->setProductId($productId);
        $entry->setUserId($userId);
        $entry->setDetails($details);
        $entry->setCreatedAt($this->dateTime->gmtDate());

        $this->auditLogRepository->save($entry);
    }
}
