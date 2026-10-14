<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlanResource;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * SubscriptionPlan.
 */
class SubscriptionPlan extends AbstractModel implements SubscriptionPlanInterface
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
        $this->_init(SubscriptionPlanResource::class);
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
    public function getProviderPlanId(): ?string
    {
        return $this->getData(self::PROVIDER_PLAN_ID);
    }

    /**
     * @param string|null $providerPlanId
     * @return void
     */
    public function setProviderPlanId(?string $providerPlanId): void
    {
        $this->setData(self::PROVIDER_PLAN_ID, $providerPlanId);
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
    public function getTemplateId(): ?int
    {
        $value = $this->getData(self::TEMPLATE_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $templateId
     * @return void
     */
    public function setTemplateId(?int $templateId): void
    {
        $this->setData(self::TEMPLATE_ID, $templateId);
    }

    /**
     * @return string|null
     */
    public function getPlanKey(): ?string
    {
        return $this->getData(self::PLAN_KEY);
    }

    /**
     * @param string|null $planKey
     * @return void
     */
    public function setPlanKey(?string $planKey): void
    {
        $this->setData(self::PLAN_KEY, $planKey);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void
    {
        $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @return int
     */
    public function getActiveSubscriptionsCount(): int
    {
        return (int)$this->getData(self::ACTIVE_SUBSCRIPTIONS_COUNT);
    }

    /**
     * @param int $count
     * @return void
     */
    public function setActiveSubscriptionsCount(int $count): void
    {
        $this->setData(self::ACTIVE_SUBSCRIPTIONS_COUNT, $count);
    }

    /**
     * @return string|null
     */
    public function getSyncStatus(): ?string
    {
        return $this->getData(self::SYNC_STATUS);
    }

    /**
     * @param string|null $syncStatus
     * @return void
     */
    public function setSyncStatus(?string $syncStatus): void
    {
        $this->setData(self::SYNC_STATUS, $syncStatus);
    }

    /**
     * @return string|null
     */
    public function getSyncError(): ?string
    {
        return $this->getData(self::SYNC_ERROR);
    }

    /**
     * @param string|null $syncError
     * @return void
     */
    public function setSyncError(?string $syncError): void
    {
        $this->setData(self::SYNC_ERROR, $syncError);
    }

    /**
     * @return string|null
     */
    public function getVersionId(): ?string
    {
        return $this->getData(self::VERSION_ID);
    }

    /**
     * @param string|null $versionId
     * @return void
     */
    public function setVersionId(?string $versionId): void
    {
        $this->setData(self::VERSION_ID, $versionId);
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->getData(self::MERCHANT_ID);
    }

    /**
     * @param string|null $merchantId
     * @return void
     */
    public function setMerchantId(?string $merchantId): void
    {
        $this->setData(self::MERCHANT_ID, $merchantId);
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
    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->setData(self::DESCRIPTION, $description);
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
    public function getRules(): array
    {
        $value = $this->getData(self::RULES);

        return $value !== null ? (array)$this->serializer->unserialize($value) : [];
    }

    /**
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules): void
    {
        $this->setData(self::RULES, $this->serializer->serialize($rules));
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
    public function getAppliesTo(): ?string
    {
        return $this->getData(self::APPLIES_TO);
    }

    /**
     * @param string|null $appliesTo
     * @return void
     */
    public function setAppliesTo(?string $appliesTo): void
    {
        $this->setData(self::APPLIES_TO, $appliesTo);
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
