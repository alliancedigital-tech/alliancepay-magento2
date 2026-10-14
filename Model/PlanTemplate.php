<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterface;
use Alliance\AlliancePay\Model\ResourceModel\PlanTemplateResource;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * PlanTemplate.
 */
class PlanTemplate extends AbstractModel implements PlanTemplateInterface
{
    private SerializerInterface $serializer;

    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        ?AbstractResource $resource = null,
        ?AbstractDbCollection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->serializer = $serializer;
        $this->_init(PlanTemplateResource::class);
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        $value = $this->getData(self::ENTITY_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param mixed $entityId
     * @return void
     */
    public function setEntityId($entityId): void
    {
        $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->getData(self::CODE);
    }

    /**
     * @param string|null $code
     * @return void
     */
    public function setCode(?string $code): void
    {
        $this->setData(self::CODE, $code);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * @return string|null
     */
    public function getCategoryIndicator(): ?string
    {
        return $this->getData(self::CATEGORY_INDICATOR);
    }

    /**
     * @param string|null $categoryIndicator
     * @return void
     */
    public function setCategoryIndicator(?string $categoryIndicator): void
    {
        $this->setData(self::CATEGORY_INDICATOR, $categoryIndicator);
    }

    /**
     * @return array
     */
    public function getRulesTemplate(): array
    {
        $value = $this->getData(self::RULES_TEMPLATE);

        return $value !== null ? (array)$this->serializer->unserialize($value) : [];
    }

    /**
     * @param array $rulesTemplate
     * @return void
     */
    public function setRulesTemplate(array $rulesTemplate): void
    {
        $this->setData(self::RULES_TEMPLATE, $this->serializer->serialize($rulesTemplate));
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string|null $status
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string|null $createdAt
     * @return void
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @param string|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
