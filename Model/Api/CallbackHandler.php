<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Api;

use Alliance\AlliancePay\Api\CallbackHandlerInterface;
use Alliance\AlliancePay\Exception\CallbackException;
use Alliance\AlliancePay\Model\Payment\Processor\CallbackProcessor;
use Alliance\AlliancePay\Logger\Logger;
use InvalidArgumentException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class CallbackHandler.
 */
class CallbackHandler implements CallbackHandlerInterface
{
    public function __construct(
        private readonly CallbackProcessor $processor,
        private readonly Request $request,
        private readonly SerializerInterface $serializer,
        private readonly Logger $logger
    ) {}

    /**
     * @return void
     */
    public function processCallback(): void
    {
        try {
            $data = $this->serializer->unserialize($this->request->getContent());
            $this->processor->processCallback($data);
        } catch (CallbackException|InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
