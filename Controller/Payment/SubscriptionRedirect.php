<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Payment;

use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Service\Subscription\SubscriptionOrderService;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * SubscriptionRedirect.
 */
class SubscriptionRedirect implements ActionInterface
{
    public function __construct(
        private readonly Session $checkoutSession,
        private readonly SubscriptionOrderService $subscriptionOrderService,
        private readonly ResultFactory $resultFactory,
        private readonly ManagerInterface $messageManager,
        private readonly Logger $logger
    ) {
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute(): ResponseInterface|ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $order = $this->checkoutSession->getLastRealOrder();

            if (!$order || !$order->getId()) {
                throw new Exception('Order not found');
            }

            $response = $this->subscriptionOrderService->createSubscriptionOrder($order);

            if (empty($response['redirectUrl'])) {
                throw new Exception((string)__('No redirect URL received from bank'));
            }

            $resultRedirect->setUrl($response['redirectUrl']);

            return $resultRedirect;
        } catch (Exception $e) {
            $this->logger->error('Subscription redirect error: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('There was an error processing your subscription. Please try again.')
            );

            return $resultRedirect->setPath('checkout/cart/index');
        }
    }
}
