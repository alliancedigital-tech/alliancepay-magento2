<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\System\Config\Authorize;

use Alliance\AlliancePay\Exception\TokenException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Alliance\AlliancePay\Service\Token\TokenAuthService;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Authorize.
 */
class Authorize extends Action
{
    public function __construct(
        private readonly Context $context,
        private readonly TokenAuthService $tokenAuthService,
        private readonly JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws TokenException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = $this->tokenAuthService->authenticate();

        $this->messageManager->addSuccessMessage('Authentication successful.');

        return $resultJson->setData([
            'is_authenticated' => $result->isAuthenticated(),
            'message' => $result->getMessage(),
        ]);
    }
}
