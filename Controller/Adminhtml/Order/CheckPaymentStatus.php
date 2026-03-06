<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\Order;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Magento\Backend\App\Action;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Payment\Processor\PaymentStatusProcessor;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class CheckPaymentStatus.
 */
class CheckPaymentStatus extends Action
{
    public function __construct(
        private readonly Context $context,
        private PaymentStatusProcessor $paymentStatusProcessor,
        private JsonFactory $jsonFactory,
        private Logger $logger
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();

        try {
            $hppOrderId = $this->getRequest()->getParam('hpp_order_id');

            if (!$hppOrderId) {
                throw new Exception('Alliance Order ID is required');
            }

            $status = $this->paymentStatusProcessor->process($hppOrderId);

            $this->messageManager->addSuccessMessage(__('Alliance Order has been updated.'));

            return $resultJson->setData([
                'success' => true,
                'status' => $status[ALLIanceOrderInterface::ORDER_STATUS]
            ]);
        } catch (Exception $e) {
            $this->logger->error('Check payment status error: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Alliance Order update failed.'));
            return $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusHeader(400);
        }
    }
}
