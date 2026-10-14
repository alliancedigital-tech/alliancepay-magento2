<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * SubscriptionPlanVersionHistoryInterface.
 */
interface SubscriptionPlanVersionHistoryInterface
{
    public const ENTITY_ID = 'entity_id';
    public const PLAN_ID = 'plan_id';
    public const OLD_VERSION_ID = 'old_version_id';
    public const NEW_VERSION_ID = 'new_version_id';
    public const OLD_RULES = 'old_rules';
    public const NEW_RULES = 'new_rules';
    public const APPLIES_FROM = 'applies_from';
    public const REASON = 'reason';
    public const CREATED_BY = 'created_by';
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
     * @return int|null
     */
    public function getPlanId(): ?int;

    /**
     * @param int|null $planId
     * @return void
     */
    public function setPlanId(?int $planId): void;

    /**
     * @return string|null
     */
    public function getOldVersionId(): ?string;

    /**
     * @param string|null $oldVersionId
     * @return void
     */
    public function setOldVersionId(?string $oldVersionId): void;

    /**
     * @return string|null
     */
    public function getNewVersionId(): ?string;

    /**
     * @param string|null $newVersionId
     * @return void
     */
    public function setNewVersionId(?string $newVersionId): void;

    /**
     * @return array|null
     */
    public function getOldRules(): ?array;

    /**
     * @param array|null $oldRules
     * @return void
     */
    public function setOldRules(?array $oldRules): void;

    /**
     * @return array
     */
    public function getNewRules(): array;

    /**
     * @param array $newRules
     * @return void
     */
    public function setNewRules(array $newRules): void;

    /**
     * @return string|null
     */
    public function getAppliesFrom(): ?string;

    /**
     * @param string|null $appliesFrom
     * @return void
     */
    public function setAppliesFrom(?string $appliesFrom): void;

    /**
     * @return string|null
     */
    public function getReason(): ?string;

    /**
     * @param string|null $reason
     * @return void
     */
    public function setReason(?string $reason): void;

    /**
     * @return int|null
     */
    public function getCreatedBy(): ?int;

    /**
     * @param int|null $createdBy
     * @return void
     */
    public function setCreatedBy(?int $createdBy): void;

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
