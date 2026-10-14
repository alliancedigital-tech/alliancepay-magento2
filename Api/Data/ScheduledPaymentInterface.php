<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * ScheduledPaymentInterface.
 */
interface ScheduledPaymentInterface
{
    public const ENTITY_ID = 'entity_id';
    public const SUBSCRIPTION_ID = 'subscription_id';
    public const PROVIDER_ORDER_ID = 'provider_order_id';
    public const SCHEDULED_PAYMENT_ID = 'scheduled_payment_id';
    public const PAYMENT_NUMBER = 'payment_number';
    public const STATUS = 'status';
    public const SCHEDULED_DATE = 'scheduled_date';
    public const EXECUTED_AT = 'executed_at';
    public const OPERATION_ID = 'operation_id';
    public const RETRY_COUNT = 'retry_count';
    public const AMOUNT_RECONCILIATION_STATUS = 'amount_reconciliation_status';
    public const RECONCILIATION_NOTE = 'reconciliation_note';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_WAITING = 'WAITING';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_FAIL = 'FAIL';
    public const STATUS_RETRY = 'RETRY';
    public const STATUS_SKIPPED_PAUSED = 'SKIPPED_PAUSED';
    public const STATUS_INVALIDATED = 'INVALIDATED';

    public const RECONCILIATION_STATUS_OK = 'OK';
    public const RECONCILIATION_STATUS_MISMATCH = 'MISMATCH';
    public const RECONCILIATION_STATUS_SKIPPED = 'SKIPPED';

    /**
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * @param int|null $entityId
     * @return void
     */
    public function setEntityId(?int $entityId): void;

    /**
     * @return int|null
     */
    public function getSubscriptionId(): ?int;

    /**
     * @param int|null $subscriptionId
     * @return void
     */
    public function setSubscriptionId(?int $subscriptionId): void;

    /**
     * @return string|null
     */
    public function getProviderOrderId(): ?string;

    /**
     * @param string|null $providerOrderId
     * @return void
     */
    public function setProviderOrderId(?string $providerOrderId): void;

    /**
     * @return string|null
     */
    public function getScheduledPaymentId(): ?string;

    /**
     * @param string|null $scheduledPaymentId
     * @return void
     */
    public function setScheduledPaymentId(?string $scheduledPaymentId): void;

    /**
     * @return int|null
     */
    public function getPaymentNumber(): ?int;

    /**
     * @param int|null $paymentNumber
     * @return void
     */
    public function setPaymentNumber(?int $paymentNumber): void;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     * @return void
     */
    public function setStatus(?string $status): void;

    /**
     * @return string|null
     */
    public function getScheduledDate(): ?string;

    /**
     * @param string|null $scheduledDate
     * @return void
     */
    public function setScheduledDate(?string $scheduledDate): void;

    /**
     * @return string|null
     */
    public function getExecutedAt(): ?string;

    /**
     * @param string|null $executedAt
     * @return void
     */
    public function setExecutedAt(?string $executedAt): void;

    /**
     * @return string|null
     */
    public function getOperationId(): ?string;

    /**
     * @param string|null $operationId
     * @return void
     */
    public function setOperationId(?string $operationId): void;

    /**
     * @return int
     */
    public function getRetryCount(): int;

    /**
     * @param int $retryCount
     * @return void
     */
    public function setRetryCount(int $retryCount): void;

    /**
     * @return string|null
     */
    public function getAmountReconciliationStatus(): ?string;

    /**
     * @param string|null $status
     * @return void
     */
    public function setAmountReconciliationStatus(?string $status): void;

    /**
     * @return string|null
     */
    public function getReconciliationNote(): ?string;

    /**
     * @param string|null $note
     * @return void
     */
    public function setReconciliationNote(?string $note): void;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     * @return void
     */
    public function setCreatedAt(?string $createdAt): void;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(?string $updatedAt): void;
}
