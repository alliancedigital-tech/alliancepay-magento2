<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config;

use Alliance\AlliancePay\Api\SensitiveDataManagerInterface;
use Alliance\AlliancePay\Logger\Logger;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;

/**
 * Class SensitiveDataManager.
 */
class SensitiveDataManager implements SensitiveDataManagerInterface
{
    public const XML_PATH_ALLIANCE_PAY_SERVICE_CODE = 'payment/alliance_payment_config/service_code';
    public const XML_PATH_ALLIANCE_PAY_AUTHORIZATION_KEY = 'payment/alliance_payment_config/authorization_key';
    public const XML_PATH_ALLIANCE_PAY_DEVICE_ID = 'payment/alliance_payment_config/device_id';
    public const XML_PATH_ALLIANCE_PAY_REFRESH_TOKEN = 'payment/alliance_payment_config/refresh_token';
    public const XML_PATH_ALLIANCE_PAY_AUTH_TOKEN = 'payment/alliance_payment_config/auth_token';
    public const XML_PATH_ALLIANCE_PAY_SERVER_PUBLIC = 'payment/alliance_payment_config/server_public';
    public const XML_PATH_ALLIANCE_PAY_EXPIRATION_DATE_TIME = 'payment/alliance_payment_config/expiration_date_time';
    public const XML_PATH_ALLIANCE_PAY_TOKEN_EXPIRATION = 'payment/alliance_payment_config/token_expiration';
    public const XML_PATH_ALLIANCE_PAY_SESSION_EXPIRATION = 'payment/alliance_payment_config/session_expiration';
    public const SENSITIVE_DATA_FIELD_REFRESH_TOKEN = 'refreshToken';
    public const SENSITIVE_DATA_FIELD_AUTH_TOKEN = 'authToken';
    public const SENSITIVE_DATA_FIELD_DEVICE_ID = 'deviceId';
    public const SENSITIVE_DATA_FIELD_SERVER_PUBLIC = 'serverPublic';
    public const SENSITIVE_DATA_FIELD_TOKEN_EXPIRATION_DATE_TIME = 'tokenExpirationDateTime';
    public const SENSITIVE_DATA_FIELD_TOKEN_EXPIRATION = 'tokenExpiration';
    public const SENSITIVE_DATA_FIELD_SESSION_EXPIRATION = 'sessionExpiration';
    public const SENSITIVE_DATA_FIELDS = [
        self::SENSITIVE_DATA_FIELD_REFRESH_TOKEN,
        self::SENSITIVE_DATA_FIELD_AUTH_TOKEN,
        self::SENSITIVE_DATA_FIELD_DEVICE_ID,
        self::SENSITIVE_DATA_FIELD_SERVER_PUBLIC,
        self::SENSITIVE_DATA_FIELD_TOKEN_EXPIRATION_DATE_TIME,
        self::SENSITIVE_DATA_FIELD_TOKEN_EXPIRATION,
        self::SENSITIVE_DATA_FIELD_SESSION_EXPIRATION,
    ];

    public function __construct(
        private readonly WriterInterface $writer,
        private readonly SerializerInterface $serializer,
        private readonly CollectionFactory $collectionFactory,
        private readonly Logger $logger
    ) {}

    /**
     * @param string $serviceCode
     * @return void
     */
    public function saveServiceCode(string $serviceCode): void
    {
        $this->writer->save(self::XML_PATH_ALLIANCE_PAY_SERVICE_CODE, $serviceCode);
    }

    /**
     * @return string
     */
    public function getServiceCode(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_SERVICE_CODE);
    }

    /**
     * @param string $authorizationKey
     * @return void
     */
    public function saveAuthorizationKey(string $authorizationKey): void
    {
        $this->writer->save(self::XML_PATH_ALLIANCE_PAY_AUTHORIZATION_KEY, $authorizationKey);
    }

    /**
     * @return string
     */
    public function getAuthorizationKey(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_AUTHORIZATION_KEY);
    }

    /**
     * @param string $refreshToken
     * @return void
     */
    public function saveRefreshToken(string $refreshToken): void
    {
        $this->saveValue(self::XML_PATH_ALLIANCE_PAY_REFRESH_TOKEN, $refreshToken);
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_REFRESH_TOKEN);
    }

    /**
     * @param string $authToken
     * @return void
     */
    public function saveAuthToken(string $authToken): void
    {
        $this->saveValue(self::XML_PATH_ALLIANCE_PAY_AUTH_TOKEN, $authToken);
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_AUTH_TOKEN);
    }

    /**
     * @param string $deviceId
     * @return void
     */
    public function saveDeviceId(string $deviceId): void
    {
        $this->saveValue(self::XML_PATH_ALLIANCE_PAY_DEVICE_ID, $deviceId);
    }

    /**
     * @return string
     */
    public function getDeviceId(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_DEVICE_ID);
    }

    /**
     * @param string $publicKey
     * @return void
     */
    public function savePublicKey(string $publicKey): void
    {
        $this->saveValue(self::XML_PATH_ALLIANCE_PAY_SERVER_PUBLIC, $publicKey);
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_SERVER_PUBLIC);
    }

    /**
     * @inheritDoc
     */
    public function saveTokenExpirationDateTime(string $tokenExpirationDate): void
    {
        $this->saveValue(self::XML_PATH_ALLIANCE_PAY_EXPIRATION_DATE_TIME, $tokenExpirationDate);
    }

    /**
     * @inheritDoc
     */
    public function getTokenExpirationDateTime(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_EXPIRATION_DATE_TIME);
    }

    /**
     * @param string $tokenExpiration
     * @return void
     */
    public function saveTokenExpiration(string $tokenExpiration): void
    {
        $this->saveValue(self::XML_PATH_ALLIANCE_PAY_TOKEN_EXPIRATION, $tokenExpiration);
    }

    /**
     * @return string
     */
    public function getTokenExpiration(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_TOKEN_EXPIRATION);
    }

    /**
     * @param string $sessionExpiration
     * @return void
     */
    public function saveSessionExpiration(string $sessionExpiration): void
    {
        $this->saveValue(self::XML_PATH_ALLIANCE_PAY_SESSION_EXPIRATION, $sessionExpiration);
    }

    /**
     * @return string
     */
    public function getSessionExpiration(): string
    {
        return $this->getUncachedConfigValue(self::XML_PATH_ALLIANCE_PAY_SESSION_EXPIRATION);
    }

    /**
     * @throws Exception
     */
    private function hasAllRequiredFields(array $result): bool
    {
        foreach (self::SENSITIVE_DATA_FIELDS as $key) {
            if (empty($result[$key])) {
                throw new Exception('Alliance payment configuration key ' . $key . ' is missing.');
            }
        }

        return true;
    }

    /**
     * @param array $result
     * @return void
     * @throws Exception
     */
    public function saveAuthorizationResult(array $result): void
    {
        if (!$this->hasAllRequiredFields($result)) {
            return;
        }

        $serverPublic = $this->serializer->serialize($result[self::SENSITIVE_DATA_FIELD_SERVER_PUBLIC]);

        try {
            $this->saveRefreshToken($result[self::SENSITIVE_DATA_FIELD_REFRESH_TOKEN]);
            $this->saveAuthToken($result[self::SENSITIVE_DATA_FIELD_AUTH_TOKEN]);
            $this->saveDeviceId($result[self::SENSITIVE_DATA_FIELD_DEVICE_ID]);
            $this->savePublicKey($serverPublic);
            $this->saveTokenExpirationDateTime(
                $result[self::SENSITIVE_DATA_FIELD_TOKEN_EXPIRATION_DATE_TIME]
            );
            $this->saveTokenExpiration($result[self::SENSITIVE_DATA_FIELD_TOKEN_EXPIRATION]);
            $this->saveSessionExpiration($result[self::SENSITIVE_DATA_FIELD_SESSION_EXPIRATION]);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @param string $configName
     * @param string $configValue
     * @return void
     */
    private function saveValue(string $configName, string $configValue): void
    {
        $this->writer->save($configName, $configValue);
    }

    /**
     * @param string $configPath
     * @param string $scope
     * @param int $scopeId
     * @return string|null
     */
    public function getUncachedConfigValue(
        string $configPath, 
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 
        int $scopeId = 0
    ): ?string {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('path', ['eq' => $configPath]);
        $collection->addFieldToFilter('scope', ['eq' => $scope]);
        $collection->addFieldToFilter('scope_id', ['eq' => $scopeId]);

        if ($collection->count() > 0) {
            return $collection->getFirstItem()->getData('value');
        }

        return '';
    }
}
