<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Alliance\AlliancePay\Model\ResourceModel\AllianceOrderResource;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class AllianceOrder.
 */
class AllianceOrder extends AbstractModel implements AllianceOrderInterface
{
    private SerializerInterface $serializer;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param SerializerInterface $serializer
     * @param AbstractResource|null $resource
     * @param AbstractDbCollection|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SerializerInterface $serializer,
        ?AbstractResource $resource = null,
        ?AbstractDbCollection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->serializer = $serializer;
        $this->_init(AllianceOrderResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): ?string
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId(?string $orderId): void
    {
        $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getMerchantRequestId(): ?string
    {
        return $this->getData(self::MERCHANT_REQUEST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMerchantRequestId(?string $merchantRequestId): void
    {
        $this->setData(self::MERCHANT_REQUEST_ID, $merchantRequestId);
    }

    /**
     * @inheritDoc
     */
    public function getHppOrderId(): ?string
    {
        return $this->getData(self::HPP_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setHppOrderId(?string $hppOrderId): void
    {
        $this->setData(self::HPP_ORDER_ID, $hppOrderId);
    }

    /**
     * @inheritDoc
     */
    public function getMerchantId(): ?string
    {
        return $this->getData(self::MERCHANT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMerchantId(?string $merchantId): void
    {
        $this->setData(self::MERCHANT_ID, $merchantId);
    }

    /**
     * @inheritDoc
     */
    public function getCoinAmount(): ?string
    {
        return $this->getData(self::COIN_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setCoinAmount(?string $coinAmount): void
    {
        $this->setData(self::COIN_AMOUNT, $coinAmount);
    }

    /**
     * @inheritDoc
     */
    public function getHppPayType(): ?string
    {
        return $this->getData(self::HPP_PAY_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setHppPayType(?string $hppPayType): void
    {
        $this->setData(self::HPP_PAY_TYPE, $hppPayType);
    }

    /**
     * @inheritDoc
     */
    public function getOrderStatus(): ?string
    {
        return $this->getData(self::ORDER_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setOrderStatus(?string $orderStatus): void
    {
        $this->setData(self::ORDER_STATUS, $orderStatus);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethods(): ?string
    {
        return $this->getData(self::PAYMENT_METHODS);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethods(string|array $paymentMethods): void
    {
        if (is_array($paymentMethods)) {
            $paymentMethods = $this->serializer->serialize($paymentMethods);
        }
        $this->setData(self::PAYMENT_METHODS, $paymentMethods);
    }

    /**
     * @inheritDoc
     */
    public function getCreateDate(): ?string
    {
        return $this->getData(self::CREATE_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setCreateDate(?string $createDate): void
    {
        $this->setData(self::CREATE_DATE, $createDate);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getOperationId(): ?string
    {
        return $this->getData(self::OPERATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOperationId(?string $operationId): void
    {
        $this->setData(self::OPERATION_ID, $operationId);
    }

    /**
     * @inheritDoc
     */
    public function getEcomOrderId(): ?string
    {
        return $this->getData(self::ECOM_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEcomOrderId(?string $ecomOrderId): void
    {
        $this->setData(self::ECOM_ORDER_ID, $ecomOrderId);
    }

    /**
     * @inheritDoc
     */
    public function getIsCallbackReturned(): ?string
    {
        return $this->getData(self::IS_CALLBACK_RETURNED);
    }

    /**
     * @inheritDoc
     */
    public function setIsCallbackReturned(bool $isCallbackReturned): void
    {
        $this->setData(self::IS_CALLBACK_RETURNED, $isCallbackReturned);
    }

    /**
     * @inheritDoc
     */
    public function getCallbackData(): ?string
    {
        return $this->getData(self::CALLBACK_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setCallbackData(string|array $callbackData): void
    {
        if (is_array($callbackData)) {
            $callbackData = $this->serializer->serialize($callbackData);
        }
        $this->setData(self::CALLBACK_DATA, $callbackData);
    }

    /**
     * @inheritDoc
     */
    public function getExpiredOrderDate(): ?string
    {
        return $this->getData(self::EXPIRED_ORDER_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setExpiredOrderDate(?string $expiredOrderDate): void
    {
        $this->setData(self::EXPIRED_ORDER_DATE, $expiredOrderDate);
    }
}
