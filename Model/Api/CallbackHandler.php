<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Api;

use Alliance\AlliancePay\Api\CallbackHandlerInterface;
use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Alliance\AlliancePay\Exception\CallbackException;
use Alliance\AlliancePay\Exception\TokenException;
use Alliance\AlliancePay\Model\Payment\Processor\CallbackProcessor;
use Alliance\AlliancePay\Model\Payment\Processor\SubscriptionCallbackProcessor;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Encryption\JwsVerifierService;
use InvalidArgumentException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class CallbackHandler.
 */
class CallbackHandler implements CallbackHandlerInterface
{
    public function __construct(
        private readonly CallbackProcessor $processor,
        private readonly SubscriptionCallbackProcessor $subscriptionProcessor,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly Request $request,
        private readonly SerializerInterface $serializer,
        private readonly JwsVerifierService $jwsVerifierService,
        private readonly Logger $logger
    ) {}

    /**
     * @return void
     */
    public function processCallback(): void
    {
        try {
            $data = $this->parseBody(trim((string)$this->request->getContent()));

            try {
                $this->subscriptionRepository->getByProviderOrderId((string)($data['hppOrderId'] ?? ''));
                $this->subscriptionProcessor->processCallback($data);
            } catch (NoSuchEntityException) {
                $this->processor->processCallback($data);
            }
        } catch (CallbackException|InvalidArgumentException|TokenException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @param string $rawBody
     * @return array
     * @throws TokenException
     */
    private function parseBody(string $rawBody): array
    {
        $this->logger->debug('Callback body: ' . $rawBody);
        if ($rawBody !== '' && !str_contains($rawBody, ' ') && substr_count($rawBody, '.') === 2) {
            return $this->jwsVerifierService->verify($rawBody);
        }

        $data = $this->serializer->unserialize($rawBody);

        if (!is_array($data)) {
            throw new InvalidArgumentException('Callback body is not a valid JSON object.');
        }
        $this->logger->debug('Callback data: ' . print_r($data, true));

        return $data;
    }
}
