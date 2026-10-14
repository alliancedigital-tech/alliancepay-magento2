<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionInterface;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionResource;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Subscription.
 */
class Subscription extends AbstractModel implements SubscriptionInterface
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
        $this->_init(SubscriptionResource::class);
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
    public function getOrderId(): ?int
    {
        $value = $this->getData(self::ORDER_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $orderId
     * @return void
     */
    public function setOrderId(?int $orderId): void
    {
        $this->setData(self::ORDER_ID, $orderId);
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
    public function getCustomerId(): ?int
    {
        $value = $this->getData(self::CUSTOMER_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $customerId
     * @return void
     */
    public function setCustomerId(?int $customerId): void
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @return array
     */
    public function getRulesSnapshot(): array
    {
        $value = $this->getData(self::RULES_SNAPSHOT);

        return $value !== null ? (array)$this->serializer->unserialize($value) : [];
    }

    /**
     * @param array $rulesSnapshot
     * @return void
     */
    public function setRulesSnapshot(array $rulesSnapshot): void
    {
        $this->setData(self::RULES_SNAPSHOT, $this->serializer->serialize($rulesSnapshot));
    }

    /**
     * @return string|null
     */
    public function getProviderOrderId(): ?string
    {
        return $this->getData(self::PROVIDER_ORDER_ID);
    }

    /**
     * @param string|null $providerOrderId
     * @return void
     */
    public function setProviderOrderId(?string $providerOrderId): void
    {
        $this->setData(self::PROVIDER_ORDER_ID, $providerOrderId);
    }

    /**
     * @return string|null
     */
    public function getMerchantRequestId(): ?string
    {
        return $this->getData(self::MERCHANT_REQUEST_ID);
    }

    /**
     * @param string|null $merchantRequestId
     * @return void
     */
    public function setMerchantRequestId(?string $merchantRequestId): void
    {
        $this->setData(self::MERCHANT_REQUEST_ID, $merchantRequestId);
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
     * @return array|null
     */
    public function getCallbackData(): ?array
    {
        $value = $this->getData(self::CALLBACK_DATA);

        return $value !== null ? (array)$this->serializer->unserialize($value) : null;
    }

    /**
     * @param array|null $callbackData
     * @return void
     */
    public function setCallbackData(?array $callbackData): void
    {
        $this->setData(self::CALLBACK_DATA, $callbackData !== null ? $this->serializer->serialize($callbackData) : null);
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
