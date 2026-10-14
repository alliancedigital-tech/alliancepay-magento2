<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionAuditLogInterface;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionAuditLogResource;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * SubscriptionAuditLog.
 */
class SubscriptionAuditLog extends AbstractModel implements SubscriptionAuditLogInterface
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
        $this->_init(SubscriptionAuditLogResource::class);
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
    public function getAction(): ?string
    {
        return $this->getData(self::ACTION);
    }

    /**
     * @param string|null $action
     * @return void
     */
    public function setAction(?string $action): void
    {
        $this->setData(self::ACTION, $action);
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
     * @return int|null
     */
    public function getProductId(): ?int
    {
        $value = $this->getData(self::PRODUCT_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $productId
     * @return void
     */
    public function setProductId(?int $productId): void
    {
        $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        $value = $this->getData(self::USER_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $userId
     * @return void
     */
    public function setUserId(?int $userId): void
    {
        $this->setData(self::USER_ID, $userId);
    }

    /**
     * @return array|null
     */
    public function getDetails(): ?array
    {
        $value = $this->getData(self::DETAILS);

        return $value !== null ? (array)$this->serializer->unserialize($value) : null;
    }

    /**
     * @param array|null $details
     * @return void
     */
    public function setDetails(?array $details): void
    {
        $this->setData(self::DETAILS, $details !== null ? $this->serializer->serialize($details) : null);
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
