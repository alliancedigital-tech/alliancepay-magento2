<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Payment\Gateway;

use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Encryption\JwsSignerService;
use Alliance\AlliancePay\Service\Encryption\JwsVerifierService;
use Alliance\AlliancePay\Service\RequestIdentification\GenerateRequestIdentification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * AbstractSignedHttpClient.
 */
abstract class AbstractSignedHttpClient
{
    private const METHOD_POST = 'POST';

    public function __construct(
        protected readonly Client $client,
        protected readonly AllianceConfig $allianceConfig,
        protected readonly SerializerInterface $serializer,
        protected readonly JwsSignerService $jwsSignerService,
        protected readonly JwsVerifierService $jwsVerifierService,
        protected readonly Logger $logger,
    ) {
    }

    /**
     * @return string
     */
    abstract protected function getContentType(): string;

    /**
     * @return array
     */
    abstract protected function getAdditionalHeaders(): array;

    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function sendRequest(string $endpoint, array $data): array
    {
        $baseUrl = $this->allianceConfig->getApiUrl();
        $payloadJson = (string)$this->serializer->serialize($data);
        $jws = $this->jwsSignerService->sign(
            (string)$this->allianceConfig->getJwsPrivateKey(),
            (string)$this->allianceConfig->getJwsKeyId(),
            $endpoint,
            $payloadJson
        );

        $options = [
            'headers' => array_merge(
                $this->getAdditionalHeaders(),
                [
                    'x-request_id' => GenerateRequestIdentification::generateRequestId(),
                    'Content-Type' => $this->getContentType(),
                    'Accept' => 'application/json',
                ]
            ),
            'body' => $jws,
        ];

        try {
            $response = $this->client->request(self::METHOD_POST, $baseUrl . $endpoint, $options);
        } catch (GuzzleException $e) {
            if ($e->getCode() === 401) {
                $this->logger->error(
                    'Request failed with 401 - check JWS Private Key and Key ID settings: ' . $e->getMessage()
                );
            }

            throw $e;
        }

        return $this->parseResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $body = trim($response->getBody()->getContents());

        if ($body !== '' && !str_contains($body, ' ') && substr_count($body, '.') === 2) {
            return $this->jwsVerifierService->verify($body);
        }

        return (array)$this->serializer->unserialize($body);
    }
}
