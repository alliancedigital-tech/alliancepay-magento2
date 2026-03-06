<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

namespace Alliance\AlliancePay\Model\Payment;

use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\Payment\Processor\RefundProcessor;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use Magento\Framework\UrlInterface;

/**
 * Alliance Payment Method
 */
class AlliancePayment extends AbstractMethod
{
    public const PAYMENT_METHOD_CODE = 'alliance_pay';
    public const PAYMENT_METHODS = ['CARD', 'APPLE_PAY', 'GOOGLE_PAY'];
    public const HPP_PAY_TYPE = 'PURCHASE';
    public const OPERATION_TYPE = 'PURCHASE';

    protected $_code = self::PAYMENT_METHOD_CODE;
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_isInitializeNeeded = false;
    protected $_canRefund = true;
    protected $_isOffline = false;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ModuleListInterface $moduleList,
        TimezoneInterface $localeDate,
        CountryFactory $countryFactory,
        private readonly AllianceConfig $allianceConfig,
        private readonly UrlInterface $urlBuilder,
        private readonly RefundProcessor $refundProcessor,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );
    }

    /**
     * @param InfoInterface $payment
     * @param $amount
     * @return $this|AlliancePayment
     * @throws LocalizedException
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        if (!$this->canAuthorize()) {
            throw new LocalizedException(__('The authorize action is not available.'));
        }
        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param $amount
     * @return $this|AlliancePayment
     * @throws LocalizedException
     */
    public function capture(InfoInterface $payment, $amount)
    {
        if (!$this->canCapture()) {
            throw new LocalizedException(__('The capture action is not available.'));
        }
        return $this;
    }

    /**
     * @param $paymentAction
     * @param $stateObject
     * @return void
     */
    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setState(Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus(Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function isActive($storeId = null): bool
    {
        return $this->allianceConfig->isEnabled();
    }

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->urlBuilder->getUrl('alliance_pay/payment/redirect');
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->urlBuilder->getUrl('checkout/onepage/success');
    }

    /**
     * @return string
     */
    public function getFailUrl()
    {
        return $this->urlBuilder->getUrl('checkout/cart/index');
    }

    /**
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->urlBuilder->getUrl('', ['_direct' => 'rest/V1/alliance_pay/callback']);
    }
}
