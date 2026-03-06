<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Payment;

use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Api\PaymentProcessorInterface;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Redirect.
 */
class Redirect implements ActionInterface
{
    public function __construct(
        private Session $checkoutSession,
        private PaymentProcessorInterface $paymentProcessor,
        private ResultFactory $resultFactory,
        private ManagerInterface $messageManager,
        private Logger $logger
    ) {
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $order = $this->checkoutSession->getLastRealOrder();

            if (!$order || !$order->getId()) {
                throw new Exception('Order not found');
            }

            $processResult = $this->paymentProcessor->process($order);

            if (!isset($processResult['redirectUrl'])) {
                throw new Exception((string)__('No redirect URL received from bank'));
            }
            $resultRedirect->setUrl($processResult['redirectUrl']);
            $this->messageManager->addSuccessMessage(__('Payment was successful.'));

            return $resultRedirect;
        } catch (Exception $e) {
            $this->logger->error('Payment redirect error: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('There was an error processing your order payment. Please try again.')
            );

            return $resultRedirect->setPath('checkout/onepage/success');
        }
    }
}
