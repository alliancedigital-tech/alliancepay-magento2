<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * AllianceOrderInterface.
 */
interface AllianceOrderInterface
{
    public const ORDER_ID = 'order_id';
    public const MERCHANT_REQUEST_ID = 'merchant_request_id';
    public const HPP_ORDER_ID = 'hpp_order_id';
    public const MERCHANT_ID = 'merchant_id';
    public const COIN_AMOUNT = 'coin_amount';
    public const HPP_PAY_TYPE = 'hpp_pay_type';
    public const ORDER_STATUS = 'order_status';
    public const PAYMENT_METHODS = 'payment_methods';
    public const CREATE_DATE = 'create_date';
    public const UPDATED_AT = 'updated_at';
    public const OPERATION_ID = 'operation_id';
    public const TRANSACTION_TYPE = 'transaction_type';
    public const TRANSACTION_TYPE_NO_REFUND = 102;
    public const ECOM_ORDER_ID = 'ecom_order_id';
    public const IS_CALLBACK_RETURNED = 'is_callback_returned';
    public const CALLBACK_DATA = 'callback_data';
    public const EXPIRED_ORDER_DATE = 'expired_order_date';
    public const OPERATION_STATUS = 'status';
    public const OPERATION_CREATION_TIME = 'creation_date_time';
    public const OPERATION_TYPE = 'type';
    public const ORDER_STATUS_SUCCESS = 'SUCCESS';
    public const ORDER_STATUS_FAIL = 'FAIL';
    public const ORDER_STATUS_PENDING = 'PENDING';
    public const ORDER_STATUS_REQUIRED_3DS = 'REQUIRED_3DS';

    /**
     * @return string|null
     */
    public function getOrderId(): ?string;

    /**
     * @param string|null $orderId
     * @return void
     */
    public function setOrderId(?string $orderId): void;

    /**
     * @return string|null
     */
    public function getMerchantRequestId(): ?string;

    /**
     * @param string|null $merchantRequestId
     * @return void
     */
    public function setMerchantRequestId(?string $merchantRequestId): void;

    /**
     * @return string|null
     */
    public function getHppOrderId(): ?string;

    /**
     * @param string|null $hppOrderId
     * @return void
     */
    public function setHppOrderId(?string $hppOrderId): void;

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
    public function getCoinAmount(): ?string;

    /**
     * @param string|null $coinAmount
     * @return void
     */
    public function setCoinAmount(?string $coinAmount): void;

    /**
     * @return string|null
     */
    public function getHppPayType(): ?string;

    /**
     * @param string|null $hppPayType
     * @return void
     */
    public function setHppPayType(?string $hppPayType): void;


    /**
     * @return string|null
     */
    public function getOrderStatus(): ?string;

    /**
     * @param string|null $orderStatus
     * @return void
     */
    public function setOrderStatus(?string $orderStatus): void;

    /**
     * @return string|null
     */
    public function getPaymentMethods(): ?string;

    /**
     * @param string|array $paymentMethods
     * @return void
     */
    public function setPaymentMethods(string|array $paymentMethods): void;

    /**
     * @return string|null
     */
    public function getCreateDate(): ?string;

    /**
     * @param string|null $createDate
     * @return void
     */
    public function setCreateDate(?string $createDate): void;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(?string $updatedAt): void;

    /**
     * @return string|null
     */
    public function getOperationId(): ?string;

    /**
     * @param string|null $operationId
     * @return void
     */
    public function setOperationId(?string $operationId): void;

    /**
     * @return int|null
     */
    public function getTransactionType(): ?int;

    /**
     * @param int|null $transactionType
     * @return void
     */
    public function setTransactionType(?int $transactionType): void;

    /**
     * @return string|null
     */
    public function getEcomOrderId(): ?string;

    /**
     * @param string|null $ecomOrderId
     * @return void
     */
    public function setEcomOrderId(?string $ecomOrderId): void;

    /**
     * @return string|null
     */
    public function getIsCallbackReturned(): ?string;

    /**
     * @param bool $isCallbackReturned
     * @return void
     */
    public function setIsCallbackReturned(bool $isCallbackReturned): void;

    /**
     * @return string|null
     */
    public function getCallbackData(): ?string;

    /**
     * @param string|array $callbackData
     * @return void
     */
    public function setCallbackData(string|array $callbackData): void;

    /**
     * @return string|null
     */
    public function getExpiredOrderDate(): ?string;

    /**
     * @param string|null $expiredOrderDate
     * @return void
     */
    public function setExpiredOrderDate(?string $expiredOrderDate): void;
}
