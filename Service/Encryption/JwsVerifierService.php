<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Encryption;

use Alliance\AlliancePay\Exception\TokenException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use SimpleJWT\InvalidTokenException;
use SimpleJWT\JWT;
use SimpleJWT\Keys\ECKey;
use SimpleJWT\Keys\KeySet;

/**
 * Class JwsVerifierService.
 */
class JwsVerifierService
{
    private const ENDPOINT_BANK_PUBLIC_KEY = '/ecom/keys/public_key/get_v1';

    private const CACHE_KEY_PREFIX = 'alliance_pay_bank_pubkey_';

    private const CACHE_LIFETIME = 3600;

    public function __construct(
        private readonly Client $client,
        private readonly AllianceConfig $allianceConfig,
        private readonly SerializerInterface $serializer,
        private readonly CacheInterface $cache,
        private readonly Logger $logger
    ) {}

    /**
     * @param string $jws
     * @return array
     * @throws TokenException
     */
    public function verify(string $jws): array
    {
        try {
            $deserialised = JWT::deserialise($jws);
        } catch (Exception $e) {
            $this->logger->error('JWS parse error: ' . $e->getMessage());
            throw new TokenException(__('JWS verification error: %1', $e->getMessage()));
        }

        $headers = $deserialised['signatures'][0]['headers'] ?? [];
        $kid = $headers['kid'] ?? null;
        $alg = $headers['alg'] ?? null;

        if (empty($kid) || empty($alg)) {
            throw new TokenException(__('JWS is missing kid or alg in its header.'));
        }

        $jwk = $this->fetchBankPublicKey((string)$kid);

        $key = new ECKey($jwk, 'php');
        $key->setKeyId((string)$kid);
        $keySet = new KeySet();
        $keySet->add($key);

        try {
            $decoded = JWT::decode($jws, $keySet, (string)$alg);
        } catch (InvalidTokenException $e) {
            $this->logger->error('JWS verification failed: ' . $e->getMessage());
            throw new TokenException(__('JWS verification failed: %1', $e->getMessage()));
        }

        return $decoded->getClaims();
    }

    /**
     * @param string $kid
     * @return array
     * @throws TokenException
     */
    private function fetchBankPublicKey(string $kid): array
    {
        $cacheKey = self::CACHE_KEY_PREFIX . md5($kid);
        $cached = $this->cache->load($cacheKey);

        if ($cached !== false) {
            return $this->serializer->unserialize($cached);
        }

        try {
            $response = $this->client->request(
                'POST',
                $this->allianceConfig->getApiUrl() . self::ENDPOINT_BANK_PUBLIC_KEY,
                ['json' => ['kid' => $kid]]
            );
            $jwk = $this->serializer->unserialize($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Cannot fetch bank public key (kid=' . $kid . '): ' . $e->getMessage()
            );
            throw new TokenException(
                __('Cannot fetch bank public key (kid=%1): %2', $kid, $e->getMessage())
            );
        }

        if (empty($jwk['kty']) || empty($jwk['crv']) || empty($jwk['x']) || empty($jwk['y'])) {
            throw new TokenException(__('Invalid public key response from bank for kid=%1.', $kid));
        }

        $this->cache->save(
            $this->serializer->serialize($jwk),
            $cacheKey,
            [],
            self::CACHE_LIFETIME
        );

        return $jwk;
    }
}
