<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanVersionHistoryInterface;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlanVersionHistoryResource;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * SubscriptionPlanVersionHistory.
 */
class SubscriptionPlanVersionHistory extends AbstractModel implements SubscriptionPlanVersionHistoryInterface
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
        $this->_init(SubscriptionPlanVersionHistoryResource::class);
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
     * @return int|null
     */
    public function getPlanId(): ?int
    {
        $value = $this->getData(self::PLAN_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $planId
     * @return void
     */
    public function setPlanId(?int $planId): void
    {
        $this->setData(self::PLAN_ID, $planId);
    }

    /**
     * @return string|null
     */
    public function getOldVersionId(): ?string
    {
        return $this->getData(self::OLD_VERSION_ID);
    }

    /**
     * @param string|null $oldVersionId
     * @return void
     */
    public function setOldVersionId(?string $oldVersionId): void
    {
        $this->setData(self::OLD_VERSION_ID, $oldVersionId);
    }

    /**
     * @return string|null
     */
    public function getNewVersionId(): ?string
    {
        return $this->getData(self::NEW_VERSION_ID);
    }

    /**
     * @param string|null $newVersionId
     * @return void
     */
    public function setNewVersionId(?string $newVersionId): void
    {
        $this->setData(self::NEW_VERSION_ID, $newVersionId);
    }

    /**
     * @return array|null
     */
    public function getOldRules(): ?array
    {
        $value = $this->getData(self::OLD_RULES);

        return $value !== null ? (array)$this->serializer->unserialize($value) : null;
    }

    /**
     * @param array|null $oldRules
     * @return void
     */
    public function setOldRules(?array $oldRules): void
    {
        $this->setData(self::OLD_RULES, $oldRules !== null ? $this->serializer->serialize($oldRules) : null);
    }

    /**
     * @return array
     */
    public function getNewRules(): array
    {
        $value = $this->getData(self::NEW_RULES);

        return $value !== null ? (array)$this->serializer->unserialize($value) : [];
    }

    /**
     * @param array $newRules
     * @return void
     */
    public function setNewRules(array $newRules): void
    {
        $this->setData(self::NEW_RULES, $this->serializer->serialize($newRules));
    }

    /**
     * @return string|null
     */
    public function getAppliesFrom(): ?string
    {
        return $this->getData(self::APPLIES_FROM);
    }

    /**
     * @param string|null $appliesFrom
     * @return void
     */
    public function setAppliesFrom(?string $appliesFrom): void
    {
        $this->setData(self::APPLIES_FROM, $appliesFrom);
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->getData(self::REASON);
    }

    /**
     * @param string|null $reason
     * @return void
     */
    public function setReason(?string $reason): void
    {
        $this->setData(self::REASON, $reason);
    }

    /**
     * @return int|null
     */
    public function getCreatedBy(): ?int
    {
        $value = $this->getData(self::CREATED_BY);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $createdBy
     * @return void
     */
    public function setCreatedBy(?int $createdBy): void
    {
        $this->setData(self::CREATED_BY, $createdBy);
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
}
