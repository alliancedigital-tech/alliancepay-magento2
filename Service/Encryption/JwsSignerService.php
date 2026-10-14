<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Encryption;

use Alliance\AlliancePay\Exception\TokenException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\RequestIdentification\GenerateRequestIdentification;
use Exception;
use SimpleJWT\JWT;
use SimpleJWT\Keys\KeyFactory;
use SimpleJWT\Keys\KeySet;
use SimpleJWT\Keys\RSAKey;

/**
 * Class JwsSignerService.
 */
class JwsSignerService
{
    private const ALGORITHM = 'RS256';

    public function __construct(
        private readonly Logger $logger
    ) {}

    /**
     * @param string $privateKeyPem
     * @param string $kid
     * @param string $targetUrl
     * @param string $payloadJson
     * @return string
     * @throws TokenException
     */
    public function sign(string $privateKeyPem, string $kid, string $targetUrl, string $payloadJson): string
    {
        if (trim($privateKeyPem) === '') {
            throw new TokenException(__('JWS Private Key (PEM) is not configured.'));
        }

        if (trim($kid) === '') {
            throw new TokenException(__('JWS Key ID (kid) is not configured.'));
        }

        $payload = json_decode($payloadJson, true);

        if (!is_array($payload) && $payload !== null) {
            throw new TokenException(__('JWS: payload is not valid JSON.'));
        }

        try {
            $key = KeyFactory::create($privateKeyPem, 'pem');
        } catch (Exception $e) {
            $this->logger->error('JWS signing key parse failed: ' . $e->getMessage());
            throw new TokenException(__('JWS Private Key is invalid: %1', $e->getMessage()));
        }

        if (!$key instanceof RSAKey) {
            throw new TokenException(
                __(
                    'JWS Private Key is invalid: expected an RSA key (PKCS#8 PEM), got a different key type.'
                )
            );
        }

        $key->setKeyId($kid);
        $keySet = new KeySet();
        $keySet->add($key);

        $jwt = new JWT(
            [
                'alg' => self::ALGORITHM,
                'ts' => (string)(int)(microtime(true) * 1000),
                'targetUrl' => $targetUrl,
                'kid' => $kid,
                'jti' => GenerateRequestIdentification::generateRequestId(),
            ],
            $payload ?? []
        );

        try {
            return $jwt->encode($keySet, kid: null, auto_complete: false);
        } catch (Exception $e) {
            $this->logger->error('JWS signing failed: ' . $e->getMessage());
            throw new TokenException(__('JWS signing failed: %1', $e->getMessage()));
        }
    }
}
