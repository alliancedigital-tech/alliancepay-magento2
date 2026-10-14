<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

use Alliance\AlliancePay\Api\Data\ScheduledPaymentInterface;

/**
 * ScheduledPaymentRepositoryInterface.
 */
interface ScheduledPaymentRepositoryInterface
{
    /**
     * @param int $id
     * @return ScheduledPaymentInterface
     */
    public function get(int $id): ScheduledPaymentInterface;

    /**
     * @param string $operationId
     * @return ScheduledPaymentInterface
     */
    public function getByOperationId(string $operationId): ScheduledPaymentInterface;

    /**
     * @param string $scheduledPaymentId
     * @return ScheduledPaymentInterface
     */
    public function getByScheduledPaymentId(string $scheduledPaymentId): ScheduledPaymentInterface;

    /**
     * @param ScheduledPaymentInterface $entity
     * @return bool
     */
    public function delete(ScheduledPaymentInterface $entity): bool;

    /**
     * @param ScheduledPaymentInterface $entity
     * @return ScheduledPaymentInterface
     */
    public function save(ScheduledPaymentInterface $entity): ScheduledPaymentInterface;
}
