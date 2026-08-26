<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Payment;

use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Api\ConvertDataServiceInterface;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Api\CustomerDataValidatorInterface;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterfaceFactory;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\Config\CountryCode\CountryCodeProvider;
use Alliance\AlliancePay\Model\Config\Source\PaymentType;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Service\Payment\Service\ServiceAbstract;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class CreateOrderService.
 */
class CreateOrderService extends ServiceAbstract
{
    public function __construct(
        private GatewayClientInterface $gatewayClient,
        private readonly CustomerRepositoryInterface $customer,
        private readonly AllianceOrderInterfaceFactory $allianceOrder,
        private readonly AllianceOrderRepositoryInterface $allianceOrderRepository,
        private readonly AlliancePayment $alliancePayment,
        private readonly AllianceConfig $allianceConfig,
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly RemoteAddress $remoteAddress,
        private readonly Resolver $localeResolver,
        private readonly ConvertDataServiceInterface $convertDataService,
        private readonly CountryCodeProvider $countryCodeProvider,
        private readonly CustomerDataValidatorInterface $customerDataValidator,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly TimezoneInterface $timezone,
        private Logger $logger
    ) {}

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function createOrder(OrderInterface $order): array
    {
        $error = [
            'success' => false,
            'message' => (string)__('Create order service error'),
        ];

        try {
            $hppOrderData = $this->preparePlaceOrderData($order);

            if ($order->getId() && !empty($hppOrderData)) {
                $resultRequest = $this->gatewayClient->createOrder($hppOrderData);
                $allianceOrder = $this->allianceOrder->create();

                if (isset($resultRequest['msgType']) && $resultRequest['msgType'] === 'ERROR') {
                    $preparedData = $this->convertDataService->camelToSnakeArrayKeys(
                        $hppOrderData,
                    );
                    $this->dataObjectHelper->populateWithArray(
                        $allianceOrder,
                        $preparedData,
                        AllianceOrderInterface::class
                    );
                    $this->allianceOrderRepository->save($allianceOrder);
                } else {
                    $preparedData = $this->convertDataService->camelToSnakeArrayKeys(
                        $resultRequest
                    );

                    $this->dataObjectHelper->populateWithArray(
                        $allianceOrder,
                        $preparedData,
                        AllianceOrderInterface::class
                    );

                    $allianceOrder->setOrderId($order->getId());
                    $isPreAuth = $hppOrderData['hppPayType'] === PaymentType::PREAUTH_PAYMENT_TYPE;

                    if ($isPreAuth) {
                        $coinAmount = $this->prepareCoinAmount((float) $order->getGrandTotal());
                        $allianceOrder->setOriginalAuthorizedAmount($coinAmount);
                    }

                    if (isset($hppOrderData['currencyCode'])) {
                        $allianceOrder->setCurrencyCode((int) $hppOrderData['currencyCode']);
                    }

                    $this->allianceOrderRepository->save($allianceOrder);

                    if ($isPreAuth) {
                        $preAuthStatus = $this->allianceConfig->getPreAuthOrderStatus();
                        if ($preAuthStatus) {
                            $order->setStatus($preAuthStatus);
                            $order->addCommentToStatusHistory(
                                __('Order placed with PREAUTH payment. Awaiting bank authorization.')
                            );
                            $this->orderRepository->save($order);
                        }
                    }
                }

                return $resultRequest;
            }
        } catch (Exception $e) {
            $this->logger->error('Create order service error: ' . $e->getMessage());
            return $error;
        }

        return $error;
    }

    /**
     * @param OrderInterface $order
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function preparePlaceOrderData(OrderInterface $order): array
    {
        $coinAmount = $this->prepareCoinAmount((float) $order->getGrandTotal());

        $data = [
            'coinAmount' => $coinAmount,
            'hppPayType' => $this->allianceConfig->getPaymentType(),
            'paymentMethods' => AlliancePayment::PAYMENT_METHODS,
            'language' => $this->getStoreLanguage(),
            'successUrl' => $this->alliancePayment->getSuccessUrl(),
            'failUrl' => $this->alliancePayment->getFailUrl(),
            'notificationUrl' => $this->alliancePayment->getCallbackUrl(),
            'merchantId' => $this->allianceConfig->getMerchantId(),
            'statusPageType' => $this->allianceConfig->getStatusPageType(),
            'merchantRequestId' => $this->generateMerchantRequestId(),
            'customerData' => $this->prepareCustomerData($order),
        ];

        if ($data['hppPayType'] === AlliancePayment::HPP_PAY_TYPE_A2A) {
            $data['directType'] = AlliancePayment::DIRECT_TYPE_BANK_LINK;
            $data['priorityBankCode'] = AlliancePayment::PRIORITY_BANK_CODE;
            $data['merchantComment'] = 'Payment for order #' . ($order->getIncrementId() ?? '');
        }

        if ($data['hppPayType'] === AlliancePayment::HPP_PAY_TYPE_PREAUTH) {
            $data['preAuthExpDate'] = $this->calculatePreAuthExpDate();
        }

        if ($data['hppPayType'] === AlliancePayment::HPP_PAY_TYPE_PURCHASE
            || $data['hppPayType'] === AlliancePayment::HPP_PAY_TYPE_PREAUTH
        ) {
            $data['currencyCode'] = $this->getCurrencyCode($order->getOrderCurrencyCode());
        }

        return $data;
    }

    /**
     * @return string
     */
    private function calculatePreAuthExpDate(): string
    {
        $expDateConfig = $this->allianceConfig->getPreAuthExpDate() ?? '2h';

        preg_match('/^(\d+)(h|d)$/', $expDateConfig, $matches);
        $value = isset($matches[1]) ? (int) $matches[1] : 2;
        $unit  = $matches[2] ?? 'h';

        $seconds = ($unit === 'h') ? $value * 3600 : $value * 86400;
        $seconds += 30;

        $date = $this->timezone->date();
        $date->modify('+' . $seconds . ' seconds');

        return $date->format('Y-m-d H:i:s.00P');
    }

    /**
     * @param OrderInterface $order
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function prepareCustomerData(OrderInterface $order): array
    {
        $data = [];
        $billingAddress = $order->getBillingAddress();

        if (!$order->getCustomerIsGuest()) {
            $customer = $this->customer->get($order->getCustomerEmail());
            $dob = $customer->getDob() ?? '';

            if (!empty($dob)) {
                $data['senderBirthday'] = $dob;
            }

            $data['senderFirstName'] = $customer->getFirstname() ?? '';
            $data['senderMiddleName'] = $customer->getMiddlename() ?? '';
            $data['senderLastName'] = $customer->getLastname() ?? '';
        } else {
            $data['senderFirstName'] = $billingAddress->getFirstname() ?? '';
            $data['senderMiddleName'] = $billingAddress->getMiddlename() ?? '';
            $data['senderLastName'] = $billingAddress->getLastname() ?? '';
        }

        $countryCode = $this->countryCodeProvider->getCountryNumericCodeByAlpha2(
            $billingAddress->getCountryId()
        );
        $data['senderCustomerId'] = $this->getCustomerId($order);
        $data['senderEmail'] = $billingAddress->getEmail() ?? '';
        $data['senderRegion'] = $billingAddress->getRegion() ?? '';
        $data['senderCountry'] = $billingAddress->getCountryId() ?? '';
        $data['senderStreet'] = $billingAddress->getStreet()[0] ?? '';
        $data['senderCity'] = $billingAddress->getCity() ?? '';
        $data['senderIp'] = $this->getCustomerIp() ?? '';
        $data['senderZipCode'] = $billingAddress->getPostcode() ?? '';
        $data['senderPhone'] = $billingAddress->getTelephone() ?? '';

        if (!empty($countryCode)) {
            $data['senderCountry'] = $countryCode;
        }

        return $this->customerDataValidator->validate($data);
    }

    /**
     * @return bool|number|string|null
     */
    private function getCustomerIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @return string
     */
    private function getStoreLanguage(): string
    {
        $locale = $this->localeResolver->getLocale();

        return strtok($locale, '_');
    }

    /**
     * @param OrderInterface $order
     * @return string
     * @throws LocalizedException
     */
    private function getCustomerId(OrderInterface $order): string
    {
        if (!$order->getCustomerIsGuest()) {
            try {
                $customer = $this->customer->get($order->getCustomerEmail());
                return (string)$customer->getId();
            } catch (NoSuchEntityException $exception) {
                //do nothing.
            }
        }

        return 'id_guest_' . $order->getId();
    }
}
