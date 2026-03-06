<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

/**
 * SensitiveDataManagerInterface.
 */
interface SensitiveDataManagerInterface
{
    /**
     * @param string $serviceCode
     * @return void
     */
    public function saveServiceCode(string $serviceCode): void;

    /**
     * @return string
     */
    public function getServiceCode(): string;

    /**
     * @param string $authorizationKey
     * @return void
     */
    public function saveAuthorizationKey(string $authorizationKey): void;

    /**
     * @return string
     */
    public function getAuthorizationKey(): string;

    /**
     * @param string $refreshToken
     * @return void
     */
    public function saveRefreshToken(string $refreshToken): void;

    /**
     * @return string
     */
    public function getRefreshToken(): string;

    /**
     * @param string $authToken
     * @return void
     */
    public function saveAuthToken(string $authToken): void;

    /**
     * @return string
     */
    public function getAuthToken(): string;

    /**
     * @param string $deviceId
     * @return void
     */
    public function saveDeviceId(string $deviceId): void;

    /**
     * @return string
     */
    public function getDeviceId(): string;

    /**
     * @param string $publicKey
     * @return void
     */
    public function savePublicKey(string $publicKey): void;

    /**
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * @param string $tokenExpirationDate
     * @return void
     */
    public function saveTokenExpirationDateTime(string $tokenExpirationDate): void;

    /**
     * @return string
     */
    public function getTokenExpirationDateTime(): string;

    /**
     * @param string $tokenExpiration
     * @return void
     */
    public function saveTokenExpiration(string $tokenExpiration): void;

    /**
     * @return string
     */
    public function getTokenExpiration(): string;

    /**
     * @param string $sessionExpiration
     * @return void
     */
    public function saveSessionExpiration(string $sessionExpiration): void;

    /**
     * @return string
     */
    public function getSessionExpiration(): string;

    /**
     * @param array $result
     * @return void
     */
    public function saveAuthorizationResult(array $result): void;
}
