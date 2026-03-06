<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api\Data;

/**
 * RefundInterface.
 */
interface RefundInterface
{
    public const REFUND_ID = 'refund_id';
    public const ORDER_ID = 'order_id';
    public const TYPE = 'type';
    public const RRN = 'rrn';
    public const PURPOSE = 'purpose';
    public const COMMENT = 'comment';
    public const COIN_AMOUNT = 'coin_amount';
    public const MERCHANT_ID = 'merchant_id';
    public const OPERATION_ID = 'operation_id';
    public const ECOM_OPERATION_ID = 'ecom_operation_id';
    public const MERCHANT_NAME = 'merchant_name';
    public const APPROVAL_CODE = 'approval_code';
    public const STATUS = 'status';
    public const TRANSACTION_TYPE = 'transaction_type';
    public const MERCHANT_REQUEST_ID = 'merchant_request_id';
    public const TRANSACTION_CURRENCY = 'transaction_currency';
    public const MERCHANT_COMMISSION = 'merchant_commission';
    public const CREATION_DATE_TIME = 'creation_date_time';
    public const MODIFICATION_DATE_TIME = 'modification_date_time';
    public const ACTION_CODE = 'action_code';
    public const RESPONSE_CODE = 'response_code';
    public const DESCRIPTION = 'description';
    public const PROCESSING_MERCHANT_ID = 'processing_merchant_id';
    public const PROCESSING_TERMINAL_ID = 'processing_terminal_id';
    public const TRANSACTION_RESPONSE_INFO = 'transaction_response_info';
    public const BANK_CODE = 'bank_code';
    public const PAYMENT_SYSTEM = 'payment_system';
    public const PRODUCT_TYPE = 'product_type';
    public const NOTIFICATION_URL = 'notification_url';
    public const PAYMENT_SERVICE_TYPE = 'payment_service_type';
    public const NOTIFICATION_ENCRYPTION = 'notification_encryption';
    public const ORIGINAL_OPERATION_ID = 'original_operation_id';
    public const ORIGINAL_COIN_AMOUNT = 'original_coin_amount';
    public const ORIGINAL_ECOM_OPERATION_ID = 'original_ecom_operation_id';
    public const RRN_ORIGINAL = 'rrn_original';
    public const REFUND_STATUS_SUCCESS = 'SUCCESS';
    public const REFUND_STATUS_PENDING = 'PENDING';
    public const REFUND_STATUS_FAIL = 'FAIL';

    /**
     * @return string|null
     */
    public function getRefundId(): ?string;

    /**
     * @param string|null $refundId
     * @return void
     */
    public function setRefundId(?string $refundId): void;

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
    public function getType(): ?string;

    /**
     * @param string|null $type
     * @return void
     */
    public function setType(?string $type): void;

    /**
     * @return string|null
     */
    public function getRrn(): ?string;

    /**
     * @param string|null $rrn
     * @return void
     */
    public function setRrn(?string $rrn): void;

    /**
     * @return string|null
     */
    public function getPurpose(): ?string;

    /**
     * @param string|null $purpose
     * @return void
     */
    public function setPurpose(?string $purpose): void;

    /**
     * @return string|null
     */
    public function getComment(): ?string;

    /**
     * @param string|null $comment
     * @return void
     */
    public function setComment(?string $comment): void;

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
    public function getMerchantId(): ?string;

    /**
     * @param string|null $merchantId
     * @return void
     */
    public function setMerchantId(?string $merchantId): void;

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
     * @return string|null
     */
    public function getEcomOperationId(): ?string;

    /**
     * @param string|null $ecomOperationId
     * @return void
     */
    public function setEcomOperationId(?string $ecomOperationId): void;

    /**
     * @return string|null
     */
    public function getMerchantName(): ?string;

    /**
     * @param string|null $merchantName
     * @return void
     */
    public function setMerchantName(?string $merchantName): void;

    /**
     * @return string|null
     */
    public function getApprovalCode(): ?string;

    /**
     * @param string|null $approvalCode
     * @return void
     */
    public function setApprovalCode(?string $approvalCode): void;

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
    public function getTransactionType(): ?string;

    /**
     * @param string|null $transactionType
     * @return void
     */
    public function setTransactionType(?string $transactionType): void;

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
    public function getTransactionCurrency(): ?string;

    /**
     * @param string|null $transactionCurrency
     * @return void
     */
    public function setTransactionCurrency(?string $transactionCurrency): void;

    /**
     * @return string|null
     */
    public function getMerchantCommission(): ?string;

    /**
     * @param string|null $merchantCommission
     * @return void
     */
    public function setMerchantCommission(?string $merchantCommission): void;

    /**
     * @return string|null
     */
    public function getCreationDateTime(): ?string;

    /**
     * @param string|null $createdDateTime
     * @return void
     */
    public function setCreationDateTime(?string $createdDateTime): void;

    /**
     * @return string|null
     */
    public function getModificationDateTime(): ?string;

    /**
     * @param string|null $modificationDateTime
     * @return void
     */
    public function setModificationDateTime(?string $modificationDateTime): void;

    /**
     * @return string|null
     */
    public function getActionCode(): ?string;

    /**
     * @param string|null $actionCode
     * @return void
     */
    public function setActionCode(?string $actionCode): void;

    /**
     * @return string|null
     */
    public function getResponseCode(): ?string;

    /**
     * @param string|null $responseCode
     * @return void
     */
    public function setResponseCode(?string $responseCode): void;

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
    public function getProcessingMerchantId(): ?string;

    /**
     * @param string|null $processingMerchantId
     * @return void
     */
    public function setProcessingMerchantId(?string $processingMerchantId): void;

    /**
     * @return string|null
     */
    public function getProcessingTerminalId(): ?string;

    /**
     * @param string|null $processingTerminalId
     * @return void
     */
    public function setProcessingTerminalId(?string $processingTerminalId): void;

    /**
     * @return string|null
     */
    public function getTransactionResponseInfo(): ?string;

    /**
     * @param string|array $transactionResponseInfo
     * @return void
     */
    public function setTransactionResponseInfo(string|array $transactionResponseInfo): void;

    /**
     * @return string|null
     */
    public function getBankCode(): ?string;

    /**
     * @param string|null $bankCode
     * @return void
     */
    public function setBankCode(?string $bankCode): void;

    /**
     * @return string|null
     */
    public function getPaymentSystem(): ?string;

    /**
     * @param string|null $paymentSystemType
     * @return void
     */
    public function setPaymentSystem(?string $paymentSystemType): void;

    /**
     * @return string|null
     */
    public function getProductType(): ?string;

    /**
     * @param string|null $productType
     * @return void
     */
    public function setProductType(?string $productType): void;

    /**
     * @return string|null
     */
    public function getNotificationUrl(): ?string;

    /**
     * @param string|null $notificationUrl
     * @return void
     */
    public function setNotificationUrl(?string $notificationUrl): void;

    /**
     * @return string|null
     */
    public function getPaymentServiceType(): ?string;

    /**
     * @param string|null $paymentServiceType
     * @return void
     */
    public function setPaymentServiceType(?string $paymentServiceType): void;

    /**
     * @return string|null
     */
    public function getNotificationEncryption(): ?string;

    /**
     * @param string|null $notificationEncryption
     * @return void
     */
    public function setNotificationEncryption(?string $notificationEncryption): void;

    /**
     * @return string|null
     */
    public function getOriginalOperationId(): ?string;

    /**
     * @param string|null $originalOperationId
     * @return void
     */
    public function setOriginalOperationId(?string $originalOperationId): void;

    /**
     * @return string|null
     */
    public function getOriginalCoinAmount(): ?string;

    /**
     * @param string|null $originalCoinAmount
     * @return void
     */
    public function setOriginalCoinAmount(?string $originalCoinAmount): void;

    /**
     * @return string|null
     */
    public function getOriginalEcomOperationId(): ?string;

    /**
     * @param string|null $originalEcomOperationId
     * @return void
     */
    public function setOriginalEcomOperationId(?string $originalEcomOperationId): void;

    /**
     * @return string|null
     */
    public function getRrnOriginal(): ?string;

    /**
     * @param string|null $originalRrn
     * @return void
     */
    public function setRrnOriginal(?string $originalRrn): void;
}
