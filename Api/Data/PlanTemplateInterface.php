<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * PlanTemplateInterface.
 */
interface PlanTemplateInterface
{
    public const ENTITY_ID = 'entity_id';
    public const CODE = 'code';
    public const NAME = 'name';
    public const CATEGORY_INDICATOR = 'category_indicator';
    public const RULES_TEMPLATE = 'rules_template';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

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
    public function getCode(): ?string;

    /**
     * @param string|null $code
     * @return void
     */
    public function setCode(?string $code): void;

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
    public function getCategoryIndicator(): ?string;

    /**
     * @param string|null $categoryIndicator
     * @return void
     */
    public function setCategoryIndicator(?string $categoryIndicator): void;

    /**
     * @return array
     */
    public function getRulesTemplate(): array;

    /**
     * @param array $rulesTemplate
     * @return void
     */
    public function setRulesTemplate(array $rulesTemplate): void;

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
