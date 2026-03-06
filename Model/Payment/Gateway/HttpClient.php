<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Gateway;

use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Api\SensitiveDataManagerInterface;
use Alliance\AlliancePay\Cron\ScheduleReAuthorization;
use Alliance\AlliancePay\Exception\TokenException;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Encryption\JweEncryptionService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * Class Client.
 */
class HttpClient implements GatewayClientInterface
{
    private const METHOD_GET = 'GET';

    private const METHOD_POST = 'POST';

    private const REQUEST_CONTENT_TYPE_TEXT = 'text/plain';

    private const REQUEST_CONTENT_TYPE_JSON = 'application/json';

    private const X_API_VERSION = 'V1';
    private const ENDPOINT_CREATE_ORDER = '/ecom/execute_request/hpp/v1/create-order';
    private const ENDPOINT_OPERATIONS = '/ecom/execute_request/hpp/v1/operations';
    private const ENDPOINT_REFUND = '/ecom/execute_request/payments/v3/refund';
    private const ENDPOINT_AUTHORIZE = '/api-gateway/authorize_virtual_device';
    private const MAX_AUTH_ATTEMPTS = 3;
    private int $authCounter = 0;

    public function __construct(
        private readonly Client $client,
        private readonly SensitiveDataManagerInterface $sensitiveDataManager,
        private readonly AllianceConfig $allianceConfig,
        private readonly SerializerInterface $serializer,
        private readonly JweEncryptionService  $jweEncryptionService,
        private readonly ScheduleReAuthorization $scheduleReAuthorization,
        private readonly Logger $logger,
    ) {}

    /**
     * @param array $orderData
     * @return array
     * @throws AlreadyExistsException
     * @throws TokenException
     */
    public function createOrder(array $orderData): array
    {
        try {
            $response = $this->sendRequest(
                self::METHOD_POST,
                self::ENDPOINT_CREATE_ORDER,
                $orderData
            );

            return $this->serializer->unserialize($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            $this->logger->error('Create order failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getResponse()->getReasonPhrase(),
            ];
        }
    }

    /**
     * @param string $hppOrderId
     * @return array
     * @throws AlreadyExistsException
     * @throws TokenException
     */
    public function getOperationStatus(string $hppOrderId): array
    {
        try {
            $response = $this->sendRequest(
                self::METHOD_POST,
                self::ENDPOINT_OPERATIONS,
                ['hppOrderId' => $hppOrderId]
            );
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $this->logger->error('Get operation status failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getResponse()->getReasonPhrase(),
            ];
        }
    }

    /**
     * @param array $refundData
     * @return array
     * @throws AlreadyExistsException
     * @throws TokenException
     */
    public function refund(array $refundData): array
    {
        try {
            $serverPublicKey = $this->serializer->unserialize($this->sensitiveDataManager->getPublicKey());
            $encryptedRefundData = $this->jweEncryptionService->encrypt(
                $refundData,
                $serverPublicKey
            );
            $response = $this->sendRequest(
                self::METHOD_POST,
                self::ENDPOINT_REFUND,
                $encryptedRefundData,
                self::REQUEST_CONTENT_TYPE_TEXT
            );
            $decodedResponse = $this->serializer->unserialize($response->getBody()->getContents());

            if (isset($decodedResponse['jwe'])) {
                $decryptedResponse = $this->jweEncryptionService->decrypt(
                    $this->sensitiveDataManager->getAuthorizationKey(),
                    $decodedResponse['jwe']
                );

                if (!empty($decryptedResponse)) {
                    return $decryptedResponse;
                }
            }
        } catch (GuzzleException $e) {
            $this->logger->error('Refund failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getResponse()->getReasonPhrase(),
            ];
        }

        return [
            'success' => false,
            'message' => __('Failed to refund order.'),
        ];
    }

    /**
     * @param string $serviceCode
     * @return array
     * @throws GuzzleException
     */
    public function authorize(string $serviceCode): array
    {
        try {
            $data = [
                'serviceCode' => $serviceCode,
            ];
            $response = $this->sendRequest(
                self::METHOD_POST,
                self::ENDPOINT_AUTHORIZE,
                $data
            );

            $result = $this->serializer->unserialize($response->getBody()->getContents());

            return $result;
        } catch (Exception $e) {
            $this->logger->error('Authorization failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param string|array|null $data
     * @param string $contentType
     * @return ResponseInterface
     * @throws AlreadyExistsException
     * @throws GuzzleException
     * @throws TokenException
     */
    private function sendRequest(
        string $method,
        string $endpoint,
        string|array $data = null,
        string $contentType = self::REQUEST_CONTENT_TYPE_JSON
    ): ResponseInterface {
        $baseUrl = $this->allianceConfig->getApiUrl();

        $options = [
            'headers' => [
                'x-api_version' => self::X_API_VERSION,
                'x-device_id' => $this->sensitiveDataManager->getDeviceId(),
                'x-refresh_token' => $this->sensitiveDataManager->getRefreshToken(),
                'x-request_id' => uniqid(),
                'Content-Type' => $contentType
            ]
        ];

        if ($contentType === self::REQUEST_CONTENT_TYPE_TEXT) {
            $options['body'] = $data;
        }

        if ($data && $contentType === self::REQUEST_CONTENT_TYPE_JSON) {
            $options['json'] = $data;
            $options['headers']['Accept'] = $contentType;
        }
        try {
            return $this->client->request($method, $baseUrl . $endpoint, $options);
        } catch (GuzzleException $e) {
            $this->logger->error('Request failed: ' . $e->getMessage());

            if ($e->getCode() === 401) {
                $reAuthResult = $this->errorAuthorizationHandler($e);
                if ($reAuthResult) {
                    $options['headers']['x-device-id'] = $this->sensitiveDataManager->getDeviceId();
                    $options['headers']['x-refresh_token'] = $this->sensitiveDataManager->getRefreshToken();

                    return $this->client->request($method, $baseUrl . $endpoint, $options);
                }
            }

            return $e->getResponse();
        }
    }

    /**
     * @param GuzzleException $e
     * @return bool
     * @throws TokenException
     * @throws AlreadyExistsException
     */
    private function errorAuthorizationHandler(GuzzleException $e): bool
    {
        if ($e->getCode() === 401 && self::MAX_AUTH_ATTEMPTS >= $this->authCounter) {
            $msgCodes = ['b_expired_token', 'b_used_token', 'b_auth_token_expired'];
            $response = $this->serializer->unserialize($e->getResponse()->getBody()->getContents());
            if (in_array($response['msgCode'], $msgCodes)) {
                $this->authCounter++;
                $result = $this->authorize($this->sensitiveDataManager->getServiceCode());
                if (!empty($result['jwe'])) {
                    $decryptResult = $this->jweEncryptionService->decrypt(
                        $this->sensitiveDataManager->getAuthorizationKey(),
                        $result['jwe']
                    );

                    if (!empty($decryptResult['refreshToken'])
                        && !empty($decryptResult['authToken'])
                        && !empty($decryptResult['deviceId'])
                        && !empty($decryptResult['serverPublic'])
                        && !empty($decryptResult['tokenExpirationDateTime'])
                        && !empty($decryptResult['tokenExpiration'])
                        && !empty($decryptResult['sessionExpiration'])
                    ) {
                        try {
                            $this->sensitiveDataManager->saveAuthorizationResult($decryptResult);

                            if (!empty($authResult['tokenExpirationDateTime'])) {
                                $this->scheduleReAuthorization->createSchedule(
                                    $authResult['tokenExpirationDateTime']
                                );
                            }
                        } catch (AlreadyExistsException $e) {
                            $this->logger->notice($e->getMessage());
                        }

                        return true;
                    }
                }
            }
        }

        return false;
    }
}
