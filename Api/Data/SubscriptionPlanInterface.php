<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * SubscriptionPlanInterface.
 */
interface SubscriptionPlanInterface
{
    public const ENTITY_ID = 'entity_id';
    public const PROVIDER_PLAN_ID = 'provider_plan_id';
    public const PRODUCT_ID = 'product_id';
    public const TEMPLATE_ID = 'template_id';
    public const PLAN_KEY = 'plan_key';
    public const IS_ACTIVE = 'is_active';
    public const ACTIVE_SUBSCRIPTIONS_COUNT = 'active_subscriptions_count';
    public const SYNC_STATUS = 'sync_status';
    public const SYNC_ERROR = 'sync_error';
    public const VERSION_ID = 'version_id';
    public const MERCHANT_ID = 'merchant_id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const STATUS = 'status';
    public const CATEGORY_INDICATOR = 'category_indicator';
    public const RULES = 'rules';
    public const APPLIES_FROM = 'applies_from';
    public const APPLIES_TO = 'applies_to';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const SYNC_STATUS_SYNCED = 'SYNCED';
    public const SYNC_STATUS_ERROR = 'ERROR';
    public const SYNC_STATUS_PENDING = 'PENDING';

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_PAUSED = 'PAUSED';
    public const STATUS_CANCELED = 'CANCELED';
    public const STATUS_ERROR = 'ERROR';

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
    public function getProviderPlanId(): ?string;

    /**
     * @param string|null $providerPlanId
     * @return void
     */
    public function setProviderPlanId(?string $providerPlanId): void;

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
    public function getTemplateId(): ?int;

    /**
     * @param int|null $templateId
     * @return void
     */
    public function setTemplateId(?int $templateId): void;

    /**
     * @return string|null
     */
    public function getPlanKey(): ?string;

    /**
     * @param string|null $planKey
     * @return void
     */
    public function setPlanKey(?string $planKey): void;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void;

    /**
     * @return int
     */
    public function getActiveSubscriptionsCount(): int;

    /**
     * @param int $count
     * @return void
     */
    public function setActiveSubscriptionsCount(int $count): void;

    /**
     * @return string|null
     */
    public function getSyncStatus(): ?string;

    /**
     * @param string|null $syncStatus
     * @return void
     */
    public function setSyncStatus(?string $syncStatus): void;

    /**
     * @return string|null
     */
    public function getSyncError(): ?string;

    /**
     * @param string|null $syncError
     * @return void
     */
    public function setSyncError(?string $syncError): void;

    /**
     * @return string|null
     */
    public function getVersionId(): ?string;

    /**
     * @param string|null $versionId
     * @return void
     */
    public function setVersionId(?string $versionId): void;

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string;

    /**
     * @param string|null $merchantId
     * @return void
     */
    public function setMerchantId(?string $merchantId): void;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void;

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
    public function getCategoryIndicator(): ?string;

    /**
     * @param string|null $categoryIndicator
     * @return void
     */
    public function setCategoryIndicator(?string $categoryIndicator): void;

    /**
     * @return array
     */
    public function getRules(): array;

    /**
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules): void;

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
    public function getAppliesTo(): ?string;

    /**
     * @param string|null $appliesTo
     * @return void
     */
    public function setAppliesTo(?string $appliesTo): void;

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
