<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Gateway;

use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Exception\TokenException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Client.
 */
class HttpClient extends AbstractSignedHttpClient implements GatewayClientInterface
{
    private const CONTENT_TYPE_JWS = 'application/jws';

    private const X_API_VERSION = 'V1';
    private const ENDPOINT_CREATE_ORDER = '/ecom/jws/payments/hpp/create_order_v1';
    private const ENDPOINT_OPERATIONS = '/ecom/jws/payments/hpp/operations_v1';
    private const ENDPOINT_REFUND = '/ecom/jws/payments/refund_v1';
    private const ENDPOINT_COMPLETION = '/ecom/jws/payments/preauth/completion_v1';

    /**
     * @param array $orderData
     * @return array
     * @throws TokenException
     */
    public function createOrder(array $orderData): array
    {
        try {
            return $this->sendRequest(self::ENDPOINT_CREATE_ORDER, $orderData);
        } catch (GuzzleException $e) {
            $this->logger->error('Create order failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getResponse()?->getReasonPhrase() ?? $e->getMessage(),
            ];
        }
    }

    /**
     * @param string $hppOrderId
     * @return array
     * @throws TokenException
     */
    public function getOperationStatus(string $hppOrderId): array
    {
        try {
            return $this->sendRequest(self::ENDPOINT_OPERATIONS, ['hppOrderId' => $hppOrderId]);
        } catch (GuzzleException $e) {
            $this->logger->error('Get operation status failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getResponse()?->getReasonPhrase() ?? $e->getMessage(),
            ];
        }
    }

    /**
     * @param array $refundData
     * @return array
     * @throws TokenException
     */
    public function refund(array $refundData): array
    {
        try {
            return $this->sendRequest(self::ENDPOINT_REFUND, $refundData);
        } catch (GuzzleException $e) {
            $this->logger->error('Refund failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getResponse()?->getReasonPhrase() ?? $e->getMessage(),
            ];
        }
    }

    /**
     * @param array $completionData
     * @return array
     * @throws TokenException
     */
    public function completion(array $completionData): array
    {
        try {
            return $this->sendRequest(self::ENDPOINT_COMPLETION, $completionData);
        } catch (GuzzleException $e) {
            $this->logger->error('Completion failed: ' . $e->getMessage());

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
        return self::CONTENT_TYPE_JWS;
    }

    /**
     * @return string[]
     */
    protected function getAdditionalHeaders(): array
    {
        return [
            'x-api_version' => self::X_API_VERSION,
        ];
    }
}
