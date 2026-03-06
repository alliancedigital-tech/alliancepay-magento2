<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Encryption;

use Alliance\AlliancePay\Exception\TokenException;
use Alliance\AlliancePay\Logger\Logger;
use Exception;
use Magento\Framework\Serialize\SerializerInterface;
use SimpleJWT\JWEFactory;
use SimpleJWT\JWE;
use SimpleJWT\Keys\KeyFactory;
use SimpleJWT\Keys\KeySetFactory;

/**
 * Class JweEncryptionService.
 */
class JweEncryptionService
{
    private const ALGORITHM = 'ECDH-ES+A256KW';

    private const ENCRYPTION = 'A256GCM';

    private array $headers = [
        'alg' => self::ALGORITHM,
        'enc' => self::ENCRYPTION
    ];

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly KeyFactory $keyFactory,
        private readonly KeySetFactory $keySetFactory,
        private readonly JWEFactory $jweFactory,
        private readonly Logger $logger
    ) {}

    /**
     * @param array $data
     * @param array $publicServerKey
     * @return string
     * @throws TokenException
     */
    public function encrypt(array $data, array $publicServerKey): string
    {
        try {
            $dataJson = $this->serializer->serialize($data);
            $key = $this->keyFactory::create($publicServerKey, alg: self::ALGORITHM);
            $keySet = $this->keySetFactory->create();
            $keySet->add($key);
            $jwe = $this->jweFactory->create(
                [
                    'headers' => $this->headers,
                    'plaintext' => $dataJson
                ]
            );

            return $jwe->encrypt($keySet);
        } catch (Exception $e) {
            $this->logger->error('Encryption failed: ' . $e->getMessage());
            throw new TokenException(__('Failed to encrypt data'));
        }
    }

    /**
     * @param string $authentificationKey
     * @param string $jweToken
     * @return array
     * @throws TokenException
     */
    public function decrypt(string $authentificationKey, string $jweToken): array
    {
        try {
            $decryptData = [];
            $key = $this->keyFactory::create(
                $authentificationKey,
                alg: self::ALGORITHM
            );
            $keySet = $this->keySetFactory->create();
            $keySet->add($key);
            $jweObj = JWE::decrypt(
                $jweToken,
                $keySet,
                self::ALGORITHM
            );

            $decryptPlainText = $jweObj->getPlaintext();

            if ($decryptPlainText) {
                $decryptData = $this->serializer->unserialize($decryptPlainText);
            }

            return $decryptData;
        } catch (Exception $e) {
            $this->logger->error('Decryption failed: ' . $e->getMessage());
            throw new TokenException(__('Failed to decrypt data'));
        }
    }
}
