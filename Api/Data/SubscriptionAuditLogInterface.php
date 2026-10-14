<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * SubscriptionAuditLogInterface.
 */
interface SubscriptionAuditLogInterface
{
    public const ENTITY_ID = 'entity_id';
    public const ACTION = 'action';
    public const PLAN_ID = 'plan_id';
    public const PRODUCT_ID = 'product_id';
    public const USER_ID = 'user_id';
    public const DETAILS = 'details';
    public const CREATED_AT = 'created_at';

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
     * @return string|null
     */
    public function getAction(): ?string;

    /**
     * @param string|null $action
     * @return void
     */
    public function setAction(?string $action): void;

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
    public function getProductId(): ?int;

    /**
     * @param int|null $productId
     * @return void
     */
    public function setProductId(?int $productId): void;

    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @param int|null $userId
     * @return void
     */
    public function setUserId(?int $userId): void;

    /**
     * @return array|null
     */
    public function getDetails(): ?array;

    /**
     * @param array|null $details
     * @return void
     */
    public function setDetails(?array $details): void;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     * @return void
     */
    public function setCreatedAt(?string $createdAt): void;
}
