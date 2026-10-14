<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\SubscriptionPlan;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * ProductPrice.
 */
class ProductPrice extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::subscription_plan';

    public function __construct(
        Context $context,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();
        $id = (int)$this->getRequest()->getParam('id');

        try {
            $product = $this->productRepository->getById($id);

            return $result->setData(['price' => (float)$product->getPrice()]);
        } catch (NoSuchEntityException) {
            return $result->setData(['price' => null]);
        }
    }
}
