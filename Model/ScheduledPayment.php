<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\ScheduledPaymentInterface;
use Alliance\AlliancePay\Model\ResourceModel\ScheduledPaymentResource;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * ScheduledPayment.
 */
class ScheduledPayment extends AbstractModel implements ScheduledPaymentInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        ?AbstractResource $resource = null,
        ?AbstractDbCollection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_init(ScheduledPaymentResource::class);
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
    public function getSubscriptionId(): ?int
    {
        $value = $this->getData(self::SUBSCRIPTION_ID);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $subscriptionId
     * @return void
     */
    public function setSubscriptionId(?int $subscriptionId): void
    {
        $this->setData(self::SUBSCRIPTION_ID, $subscriptionId);
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
    public function getScheduledPaymentId(): ?string
    {
        return $this->getData(self::SCHEDULED_PAYMENT_ID);
    }

    /**
     * @param string|null $scheduledPaymentId
     * @return void
     */
    public function setScheduledPaymentId(?string $scheduledPaymentId): void
    {
        $this->setData(self::SCHEDULED_PAYMENT_ID, $scheduledPaymentId);
    }

    /**
     * @return int|null
     */
    public function getPaymentNumber(): ?int
    {
        $value = $this->getData(self::PAYMENT_NUMBER);

        return $value !== null ? (int)$value : null;
    }

    /**
     * @param int|null $paymentNumber
     * @return void
     */
    public function setPaymentNumber(?int $paymentNumber): void
    {
        $this->setData(self::PAYMENT_NUMBER, $paymentNumber);
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
    public function getScheduledDate(): ?string
    {
        return $this->getData(self::SCHEDULED_DATE);
    }

    /**
     * @param string|null $scheduledDate
     * @return void
     */
    public function setScheduledDate(?string $scheduledDate): void
    {
        $this->setData(self::SCHEDULED_DATE, $scheduledDate);
    }

    /**
     * @return string|null
     */
    public function getExecutedAt(): ?string
    {
        return $this->getData(self::EXECUTED_AT);
    }

    /**
     * @param string|null $executedAt
     * @return void
     */
    public function setExecutedAt(?string $executedAt): void
    {
        $this->setData(self::EXECUTED_AT, $executedAt);
    }

    /**
     * @return string|null
     */
    public function getOperationId(): ?string
    {
        return $this->getData(self::OPERATION_ID);
    }

    /**
     * @param string|null $operationId
     * @return void
     */
    public function setOperationId(?string $operationId): void
    {
        $this->setData(self::OPERATION_ID, $operationId);
    }

    /**
     * @return int
     */
    public function getRetryCount(): int
    {
        return (int)$this->getData(self::RETRY_COUNT);
    }

    /**
     * @param int $retryCount
     * @return void
     */
    public function setRetryCount(int $retryCount): void
    {
        $this->setData(self::RETRY_COUNT, $retryCount);
    }

    /**
     * @return string|null
     */
    public function getAmountReconciliationStatus(): ?string
    {
        return $this->getData(self::AMOUNT_RECONCILIATION_STATUS);
    }

    /**
     * @param string|null $status
     * @return void
     */
    public function setAmountReconciliationStatus(?string $status): void
    {
        $this->setData(self::AMOUNT_RECONCILIATION_STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getReconciliationNote(): ?string
    {
        return $this->getData(self::RECONCILIATION_NOTE);
    }

    /**
     * @param string|null $note
     * @return void
     */
    public function setReconciliationNote(?string $note): void
    {
        $this->setData(self::RECONCILIATION_NOTE, $note);
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
