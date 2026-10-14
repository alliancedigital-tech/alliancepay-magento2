<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * SubscriptionInterface.
 */
interface SubscriptionInterface
{
    public const ENTITY_ID = 'entity_id';
    public const ORDER_ID = 'order_id';
    public const PLAN_ID = 'plan_id';
    public const CUSTOMER_ID = 'customer_id';
    public const RULES_SNAPSHOT = 'rules_snapshot';
    public const PROVIDER_ORDER_ID = 'provider_order_id';
    public const MERCHANT_REQUEST_ID = 'merchant_request_id';
    public const STATUS = 'status';
    public const CALLBACK_DATA = 'callback_data';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_CANCELED = 'CANCELED';
    public const STATUS_FAILED = 'FAILED';

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
    public function getOrderId(): ?int;

    /**
     * @param int|null $orderId
     * @return void
     */
    public function setOrderId(?int $orderId): void;

    /**
     * @return int|null
     */
    public function getPlanId(): ?int;

    /**
     * @param int|null $planId
     * @return void
     */
    public function setPlanId(?int $planId): void;

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * @param int|null $customerId
     * @return void
     */
    public function setCustomerId(?int $customerId): void;

    /**
     * @return array
     */
    public function getRulesSnapshot(): array;

    /**
     * @param array $rulesSnapshot
     * @return void
     */
    public function setRulesSnapshot(array $rulesSnapshot): void;

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
    public function getMerchantRequestId(): ?string;

    /**
     * @param string|null $merchantRequestId
     * @return void
     */
    public function setMerchantRequestId(?string $merchantRequestId): void;

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
     * @return array|null
     */
    public function getCallbackData(): ?array;

    /**
     * @param array|null $callbackData
     * @return void
     */
    public function setCallbackData(?array $callbackData): void;

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
