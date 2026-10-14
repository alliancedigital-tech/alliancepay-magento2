<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\CustomerDataValidatorInterface;
use Alliance\AlliancePay\Api\Data\SubscriptionInterface;
use Alliance\AlliancePay\Api\Data\SubscriptionInterfaceFactory;
use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Api\ScheduledPaymentGatewayClientInterface;
use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Alliance\AlliancePay\Exception\SubscriptionException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\Config\CountryCode\CountryCodeProvider;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Alliance\AlliancePay\Model\Payment\AlliancePaySubscriptionPayment;
use Alliance\AlliancePay\Service\Payment\Service\ServiceAbstract;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * SubscriptionOrderService.
 */
class SubscriptionOrderService extends ServiceAbstract
{
    public function __construct(
        private readonly ScheduledPaymentGatewayClientInterface $gatewayClient,
        private readonly SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly SubscriptionInterfaceFactory $subscriptionFactory,
        private readonly AlliancePayment $alliancePayment,
        private readonly AlliancePaySubscriptionPayment $alliancePaySubscriptionPayment,
        private readonly AllianceConfig $allianceConfig,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CountryCodeProvider $countryCodeProvider,
        private readonly CustomerDataValidatorInterface $customerDataValidator,
        private readonly RemoteAddress $remoteAddress,
        private readonly DateTime $dateTime,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param OrderInterface $order
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws SubscriptionException
     */
    public function createSubscriptionOrder(OrderInterface $order): array
    {
        $item = current($order->getAllVisibleItems() ?: $order->getAllItems());

        if (!$item) {
            throw new SubscriptionException(__('Order does not contain a subscription product.'));
        }

        $plan = $this->resolvePlan($item);

        $payload = [
            'planId' => $plan->getProviderPlanId(),
            'merchantId' => $this->allianceConfig->getMerchantId(),
            'merchantRequestId' => $this->generateMerchantRequestId(),
            'purpose' => 'Subscription for order #' . ($order->getIncrementId() ?? ''),
            'merchantComment' => 'Subscription for order #' . ($order->getIncrementId() ?? ''),
            'notificationUrl' => $this->alliancePayment->getCallbackUrl(),
            'notificationEncryption' => 'JWS',
            'statusPageType' => $this->allianceConfig->getStatusPageType(),
            'successUrl' => $this->alliancePayment->getSuccessUrl(),
            'failUrl' => $this->alliancePayment->getFailUrl(),
            'hppPaymentMethods' => AlliancePayment::PAYMENT_METHODS,
            'directType' => AlliancePaySubscriptionPayment::DIRECT_TYPE_REDIRECT,
            'customerData' => $this->prepareCustomerData($order),
        ];

        $response = $this->gatewayClient->createSubscriptionOrder($payload);

        if (empty($response['hppOrderId']) || empty($response['redirectUrl'])) {
            $this->logger->error('Subscription order creation failed: ' . json_encode($response));
            throw new SubscriptionException(__('Could not create subscription order on the provider side.'));
        }

        $subscription = $this->subscriptionFactory->create();
        $subscription->setOrderId((int)$order->getId());
        $subscription->setPlanId($plan->getEntityId());
        $subscription->setCustomerId((int)$order->getCustomerId());
        $subscription->setRulesSnapshot($plan->getRules());
        $subscription->setProviderOrderId((string)$response['hppOrderId']);
        $subscription->setMerchantRequestId($payload['merchantRequestId']);
        $subscription->setStatus(SubscriptionInterface::STATUS_PENDING);
        $subscription->setCreatedAt($this->dateTime->gmtDate());
        $this->subscriptionRepository->save($subscription);

        return $response;
    }

    /**
     * @param OrderItemInterface $item
     * @return SubscriptionPlanInterface
     */
    private function resolvePlan(OrderItemInterface $item): SubscriptionPlanInterface
    {
        $planId = (int)($item->getBuyRequest()->getData('alliance_plan_id') ?? 0);

        if ($planId) {
            try {
                $plan = $this->subscriptionPlanRepository->get($planId);

                if ($plan->isActive() && (int)$plan->getProductId() === (int)$item->getProductId()) {
                    return $plan;
                }
            } catch (NoSuchEntityException) {
            }
        }

        return $this->subscriptionPlanRepository->getActiveByProductId((int)$item->getProductId());
    }

    /**
     * @param OrderInterface $order
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function prepareCustomerData(OrderInterface $order): array
    {
        $billingAddress = $order->getBillingAddress();
        $customer = $this->customerRepository->get($order->getCustomerEmail());

        $data = [
            'senderFirstName' => $customer->getFirstname() ?? '',
            'senderMiddleName' => $customer->getMiddlename() ?? '',
            'senderLastName' => $customer->getLastname() ?? '',
            'senderCustomerId' => (string)$customer->getId(),
            'senderEmail' => $billingAddress->getEmail() ?? '',
            'senderRegion' => $billingAddress->getRegion() ?? '',
            'senderCountry' => $billingAddress->getCountryId() ?? '',
            'senderStreet' => $billingAddress->getStreet()[0] ?? '',
            'senderCity' => $billingAddress->getCity() ?? '',
            'senderIp' => $this->remoteAddress->getRemoteAddress() ?? '',
            'senderZipCode' => $billingAddress->getPostcode() ?? '',
            'senderPhone' => $billingAddress->getTelephone() ?? '',
        ];

        $countryCode = $this->countryCodeProvider->getCountryNumericCodeByAlpha2(
            $billingAddress->getCountryId()
        );

        if (!empty($countryCode)) {
            $data['senderCountry'] = $countryCode;
        }

        return $this->customerDataValidator->validate($data);
    }
}
