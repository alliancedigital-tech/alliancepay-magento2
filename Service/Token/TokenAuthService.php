<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Token;

use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Api\SensitiveDataManagerInterface;
use Alliance\AlliancePay\Service\Encryption\JweEncryptionService;
use Alliance\AlliancePay\Cron\ScheduleReAuthorization;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Exception\TokenException;
use Exception;

/**
 * Class TokenAuthService.
 */
class TokenAuthService
{
    private string $message;

    private bool $isAuthenticated;

    public function __construct(
        private readonly SensitiveDataManagerInterface $sensitiveDataManager,
        private readonly JweEncryptionService $jweEncryptionService,
        private readonly GatewayClientInterface $gatewayClient,
        private readonly ScheduleReAuthorization $scheduleReAuthorization,
        private readonly Logger $logger
    ) {}

    /**
     * @return $this
     * @throws TokenException
     */
    public function authenticate(): TokenAuthService
    {
        try {
            $serviceCode = $this->sensitiveDataManager->getServiceCode();
            $authResult = $this->gatewayClient->authorize($serviceCode);

            if (!empty($authResult['jwe'])) {
                $authorizationKey = $this->sensitiveDataManager->getAuthorizationKey();
                $authData = $this->jweEncryptionService->decrypt(
                    $authorizationKey,
                    $authResult['jwe']
                );
                $this->sensitiveDataManager->saveAuthorizationResult($authData);

                if (!empty($authResult['tokenExpirationDateTime'])) {
                    $this->scheduleReAuthorization->createSchedule($authResult['tokenExpirationDateTime']);
                }

                $this->setIsAuthenticated(true);
                $this->setMessage((string)__('Token authentication successful.'));
            } elseif (isset($authResult['msgType']) && $authResult['msgType'] === 'ERROR') {
                $this->setIsAuthenticated(false);
                $this->setMessage((string)__($authResult['msgText']));
            } else {
                $this->setIsAuthenticated(false);
                $this->setMessage((string)__('Token authentication failed.'));
            }
        } catch (Exception $e) {
            $this->setIsAuthenticated(false);
            $this->setMessage((string)__('Token authentication failed.'));
            $this->logger->error('Token authentication failed: ' . $e->getMessage());
            throw new TokenException(__('Failed to authenticate: %1', $e->getMessage()));
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param bool $isAuthenticated
     * @return void
     */
    private function setIsAuthenticated(bool $isAuthenticated): void
    {
        $this->isAuthenticated = $isAuthenticated;
    }

    /**
     * @param string $message
     * @return void
     */
    private function setMessage(string $message)
    {
        $this->message = $message;
    }
}
