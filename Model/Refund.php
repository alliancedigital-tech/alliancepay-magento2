<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\RefundInterface;
use Alliance\AlliancePay\Model\ResourceModel\RefundResource;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Refund.
 */
class Refund extends AbstractModel implements RefundInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        private SerializerInterface $serializer,
        private DateTime $dateTime,
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
    }

    public function _construct()
    {
        $this->_init(RefundResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getRefundId(): ?string
    {
        return $this->getData(self::REFUND_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRefundId(?string $refundId): void
    {
        $this->setData(self::REFUND_ID, $refundId);
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
    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(?string $type): void
    {
        $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getRrn(): ?string
    {
        return $this->getData(self::RRN);
    }

    /**
     * @inheritDoc
     */
    public function setRrn(?string $rrn): void
    {
        $this->setData(self::RRN, $rrn);
    }

    /**
     * @inheritDoc
     */
    public function getPurpose(): ?string
    {
        return $this->getData(self::PURPOSE);
    }

    /**
     * @inheritDoc
     */
    public function setPurpose(?string $purpose): void
    {
        $this->setData(self::PURPOSE, $purpose);
    }

    /**
     * @inheritDoc
     */
    public function getComment(): ?string
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * @inheritDoc
     */
    public function setComment(?string $comment): void
    {
        $this->setData(self::COMMENT, $comment);
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
    public function getEcomOperationId(): ?string
    {
        return $this->getData(self::ECOM_OPERATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEcomOperationId(?string $ecomOperationId): void
    {
        $this->setData(self::ECOM_OPERATION_ID, $ecomOperationId);
    }

    /**
     * @inheritDoc
     */
    public function getMerchantName(): ?string
    {
        return $this->getData(self::MERCHANT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setMerchantName(?string $merchantName): void
    {
        $this->setData(self::MERCHANT_NAME, $merchantName);
    }

    /**
     * @inheritDoc
     */
    public function getApprovalCode(): ?string
    {
        return $this->getData(self::APPROVAL_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setApprovalCode(?string $approvalCode): void
    {
        $this->setData(self::APPROVAL_CODE, $approvalCode);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(?string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionType(): ?string
    {
        return $this->getData(self::TRANSACTION_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionType(?string $transactionType): void
    {
        $this->setData(self::TRANSACTION_TYPE, $transactionType);
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
    public function getTransactionCurrency(): ?string
    {
        return $this->getData(self::TRANSACTION_CURRENCY);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionCurrency(?string $transactionCurrency): void
    {
        $this->setData(self::TRANSACTION_CURRENCY, $transactionCurrency);
    }

    /**
     * @inheritDoc
     */
    public function getMerchantCommission(): ?string
    {
        return $this->getData(self::MERCHANT_COMMISSION);
    }

    /**
     * @inheritDoc
     */
    public function setMerchantCommission(?string $merchantCommission): void
    {
        $this->setData(self::MERCHANT_COMMISSION, $merchantCommission);
    }

    /**
     * @inheritDoc
     */
    public function getCreationDateTime(): ?string
    {
        return $this->getData(self::CREATION_DATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setCreationDateTime(?string $createdDateTime): void
    {
        $createdDateTime = $this->dateTime->date('Y-m-d H:i:s', $createdDateTime);
        $this->setData(self::CREATION_DATE_TIME, $createdDateTime);
    }

    /**
     * @inheritDoc
     */
    public function getModificationDateTime(): ?string
    {
        return $this->getData(self::MODIFICATION_DATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setModificationDateTime(?string $modificationDateTime): void
    {
        $modificationDateTime = $this->dateTime->date('Y-m-d H:i:s', $modificationDateTime);
        $this->setData(self::MODIFICATION_DATE_TIME, $modificationDateTime);
    }

    /**
     * @inheritDoc
     */
    public function getActionCode(): ?string
    {
        return $this->getData(self::ACTION_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setActionCode(?string $actionCode): void
    {
        $this->setData(self::ACTION_CODE, $actionCode);
    }

    /**
     * @inheritDoc
     */
    public function getResponseCode(): ?string
    {
        return $this->getData(self::RESPONSE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setResponseCode(?string $responseCode): void
    {
        $this->setData(self::RESPONSE_CODE, $responseCode);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(?string $description): void
    {
        $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getProcessingMerchantId(): ?string
    {
        return $this->getData(self::PROCESSING_MERCHANT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProcessingMerchantId(?string $processingMerchantId): void
    {
        $this->setData(self::PROCESSING_MERCHANT_ID, $processingMerchantId);
    }

    /**
     * @inheritDoc
     */
    public function getProcessingTerminalId(): ?string
    {
        return $this->getData(self::PROCESSING_TERMINAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProcessingTerminalId(?string $processingTerminalId): void
    {
        $this->setData(self::PROCESSING_TERMINAL_ID, $processingTerminalId);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionResponseInfo(): ?string
    {
        return $this->getData(self::TRANSACTION_RESPONSE_INFO);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionResponseInfo(string|array $transactionResponseInfo): void
    {
        if (is_array($transactionResponseInfo)) {
            $transactionResponseInfo = $this->serializer->serialize($transactionResponseInfo);
        }
        $this->setData(self::TRANSACTION_RESPONSE_INFO, $transactionResponseInfo);
    }

    /**
     * @inheritDoc
     */
    public function getBankCode(): ?string
    {
        return $this->getData(self::BANK_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setBankCode(?string $bankCode): void
    {
        $this->setData(self::BANK_CODE, $bankCode);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentSystem(): ?string
    {
        return $this->getData(self::PAYMENT_SYSTEM);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentSystem(?string $paymentSystemType): void
    {
        $this->setData(self::PAYMENT_SYSTEM, $paymentSystemType);
    }

    /**
     * @inheritDoc
     */
    public function getProductType(): ?string
    {
        return $this->getData(self::PRODUCT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setProductType(?string $productType): void
    {
        $this->setData(self::PRODUCT_TYPE, $productType);
    }

    /**
     * @inheritDoc
     */
    public function getNotificationUrl(): ?string
    {
        return $this->getData(self::NOTIFICATION_URL);
    }

    /**
     * @inheritDoc
     */
    public function setNotificationUrl(?string $notificationUrl): void
    {
        $this->setData(self::NOTIFICATION_URL, $notificationUrl);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentServiceType(): ?string
    {
        return $this->getData(self::PAYMENT_SERVICE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentServiceType(?string $paymentServiceType): void
    {
        $this->setData(self::PAYMENT_SERVICE_TYPE, $paymentServiceType);
    }

    /**
     * @inheritDoc
     */
    public function getNotificationEncryption(): ?string
    {
        return $this->getData(self::NOTIFICATION_ENCRYPTION);
    }

    /**
     * @inheritDoc
     */
    public function setNotificationEncryption(?string $notificationEncryption): void
    {
        $this->setData(self::NOTIFICATION_ENCRYPTION, $notificationEncryption);
    }

    /**
     * @inheritDoc
     */
    public function getOriginalOperationId(): ?string
    {
        return $this->getData(self::ORIGINAL_OPERATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOriginalOperationId(?string $originalOperationId): void
    {
        $this->setData(self::ORIGINAL_OPERATION_ID, $originalOperationId);
    }

    /**
     * @inheritDoc
     */
    public function getOriginalCoinAmount(): ?string
    {
        return $this->getData(self::ORIGINAL_COIN_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setOriginalCoinAmount(?string $originalCoinAmount): void
    {
        $this->setData(self::ORIGINAL_COIN_AMOUNT, $originalCoinAmount);
    }

    /**
     * @inheritDoc
     */
    public function getOriginalEcomOperationId(): ?string
    {
        return $this->getData(self::ORIGINAL_ECOM_OPERATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOriginalEcomOperationId(?string $originalEcomOperationId): void
    {
        $this->setData(self::ORIGINAL_ECOM_OPERATION_ID, $originalEcomOperationId);
    }

    /**
     * @inheritDoc
     */
    public function getRrnOriginal(): ?string
    {
        return $this->getData(self::RRN_ORIGINAL);
    }

    /**
     * @inheritDoc
     */
    public function setRrnOriginal(?string $originalRrn): void
    {
        $this->setData(self::RRN_ORIGINAL, $originalRrn);
    }
}
