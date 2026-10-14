<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Gateway;

use Alliance\AlliancePay\Api\ScheduledPaymentGatewayClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ScheduledPaymentHttpClient.
 */
class ScheduledPaymentHttpClient extends AbstractSignedHttpClient implements ScheduledPaymentGatewayClientInterface
{
    private const CONTENT_TYPE_JWT = 'application/jwt';

    private const ENDPOINT_PLAN_CREATE = '/ecom/jws/scheduled_payments/plan/create_v1';
    private const ENDPOINT_PLAN_UPDATE = '/ecom/jws/scheduled_payments/plan/update_v1';
    private const ENDPOINT_PLAN_STATUS_UPDATE = '/ecom/jws/scheduled_payments/plan/status_update_v1';
    private const ENDPOINT_PLAN_VERSION_UPDATE = '/ecom/jws/scheduled_payments/plan/version/update_v1';
    private const ENDPOINT_VERSION_DELETE = '/ecom/jws/scheduled_payments/version/delete_v1';
    private const ENDPOINT_SUBSCRIPTION_ORDER_CREATE = '/ecom/jws/scheduled_payments/subscription-orders/create_v1';
    private const ENDPOINT_SUBSCRIPTION_ORDER_CANCEL = '/ecom/jws/scheduled_payments/subscription-orders/cancel_v1';
    private const ENDPOINT_RECURRING_PAYMENT = '/ecom/jws/scheduled_payments/payments/recurring_subscription_v1';

    /**
     * @param array $data
     * @return array
     */
    public function createPlan(array $data): array
    {
        return $this->send(self::ENDPOINT_PLAN_CREATE, $data, 'Create plan failed');
    }

    /**
     * @param array $data
     * @return array
     */
    public function updatePlan(array $data): array
    {
        return $this->send(self::ENDPOINT_PLAN_UPDATE, $data, 'Update plan failed');
    }

    /**
     * @param array $data
     * @return array
     */
    public function updatePlanStatus(array $data): array
    {
        return $this->send(self::ENDPOINT_PLAN_STATUS_UPDATE, $data, 'Update plan status failed');
    }

    /**
     * @param array $data
     * @return array
     */
    public function updatePlanVersion(array $data): array
    {
        return $this->send(self::ENDPOINT_PLAN_VERSION_UPDATE, $data, 'Update plan version failed');
    }

    /**
     * @param array $data
     * @return array
     */
    public function deleteVersion(array $data): array
    {
        return $this->send(self::ENDPOINT_VERSION_DELETE, $data, 'Delete version failed');
    }

    /**
     * @param array $data
     * @return array
     */
    public function createSubscriptionOrder(array $data): array
    {
        return $this->send(self::ENDPOINT_SUBSCRIPTION_ORDER_CREATE, $data, 'Create subscription order failed');
    }

    /**
     * @param array $data
     * @return array
     */
    public function cancelSubscriptionOrder(array $data): array
    {
        return $this->send(self::ENDPOINT_SUBSCRIPTION_ORDER_CANCEL, $data, 'Cancel subscription order failed');
    }

    /**
     * @param array $data
     * @return array
     */
    public function triggerRecurringPayment(array $data): array
    {
        return $this->send(self::ENDPOINT_RECURRING_PAYMENT, $data, 'Trigger recurring payment failed');
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @param string $errorMessage
     * @return array
     */
    private function send(string $endpoint, array $data, string $errorMessage): array
    {
        try {
            return $this->sendRequest($endpoint, $data);
        } catch (GuzzleException $e) {
            $this->logger->error($errorMessage . ': ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getResponse()?->getReasonPhrase() ?? $e->getMessage(),
            ];
        }
    }

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return self::CONTENT_TYPE_JWT;
    }

    /**
     * @return array
     */
    protected function getAdditionalHeaders(): array
    {
        return [];
    }
}
