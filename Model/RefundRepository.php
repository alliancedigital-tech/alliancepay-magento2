<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\RefundInterface;
use Alliance\AlliancePay\Api\RefundRepositoryInterface;
use Alliance\AlliancePay\Model\ResourceModel\RefundResource;
use Alliance\AlliancePay\Api\Data\RefundInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class RefundRepository implements RefundRepositoryInterface
{
    public function __construct(
        private RefundResource $resource,
        private RefundInterfaceFactory $refundFactory
    ) {}

    /**
     * @inheritDoc
     */
    public function save(RefundInterface $refund): RefundInterface
    {
        $this->resource->save($refund);

        return $refund;
    }

    /**
     * @inheritDoc
     */
    public function get($id): RefundInterface
    {
        $refund = $this->refundFactory->create();
        $this->resource->load($refund, $id);
        if (!$refund->getId()) {
            throw new NoSuchEntityException(__('Refund with ID "%1" not found.', $id));
        }

        return $refund;
    }

    /**
     * @inheritDoc
     */
    public function delete(RefundInterface $refund): bool
    {
        $this->resource->delete($refund);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getByOrderId(string $orderId): RefundInterface
    {
        $refund = $this->refundFactory->create();
        $this->resource->load($refund, $orderId, RefundInterface::ORDER_ID);
        if (!$refund->getOrderId()) {
            throw new NoSuchEntityException(__('Refund with order ID "%1" not found.', $orderId));
        }

        return $refund;
    }
}
