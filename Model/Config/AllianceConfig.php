<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config;

use Alliance\AlliancePay\Model\Config\Source\StatusPageType;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class AllianceConfig.
 */
class AllianceConfig
{
    public const XML_PATH_ALLIANCE_PAY_IS_ENABLED = 'payment/alliance_payment_config/enabled';
    public const XML_PATH_ALLIANCE_PAY_TITLE = 'payment/alliance_payment_config/title';
    public const XML_PATH_ALLIANCE_PAY_API_URL = 'payment/alliance_payment_config/api_url';
    public const XML_PATH_ALLIANCE_PAY_MERCHANT_ID = 'payment/alliance_payment_config/merchant_id';
    public const XML_PATH_ALLIANCE_PAY_STATUS_PAGE_TYPE = 'payment/alliance_payment_config/status_page_type';
    public const XML_PATH_ALLIANCE_PAY_SUCCESS_ORDER_STATUS = 'payment/alliance_payment_config/success_order_status';
    public const XML_PATH_ALLIANCE_PAY_FAILED_ORDER_STATUS = 'payment/alliance_payment_config/failed_order_status';
    public const XML_PATH_ALLIANCE_PAY_SUCCESS_REFUND_ORDER_STATUS = 'payment/alliance_payment_config/success_refund_order_status';
    public const XML_PATH_ALLIANCE_PAY_FAILED_REFUND_ORDER_STATUS = 'payment/alliance_payment_config/failed_refund_order_status';
    public const XML_PATH_ALLIANCE_PAY_IS_LOGS_ENABLED = 'payment/alliance_payment_config/is_logs_enabled';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ALLIANCE_PAY_IS_ENABLED);
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_TITLE);
    }

    /**
     * @return string|null
     */
    public function getApiUrl(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_API_URL);
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_MERCHANT_ID);
    }

    /**
     * @return string|null
     */
    public function getStatusPageType(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_STATUS_PAGE_TYPE) ?? StatusPageType::DEFAULT_STATUS_PAGE_TYPE;
    }

    /**
     * @return string|null
     */
    public function getSuccessOrderStatus(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_SUCCESS_ORDER_STATUS);
    }

    /**
     * @return string|null
     */
    public function getFailedOrderStatus(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_FAILED_ORDER_STATUS);
    }

    /**
     * @return string|null
     */
    public function getSuccessRefundOrderStatus(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_SUCCESS_REFUND_ORDER_STATUS);
    }

    /**
     * @return string|null
     */
    public function getFailedRefundOrderStatus(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLIANCE_PAY_FAILED_REFUND_ORDER_STATUS);
    }

    /**
     * @return bool
     */
    public function isLogsEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ALLIANCE_PAY_IS_LOGS_ENABLED);
    }
}
