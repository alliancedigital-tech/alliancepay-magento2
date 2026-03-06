<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\ResourceModel\AllianceOrderResource;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AllianceOrderRepository.
 */
class AllianceOrderRepository implements AllianceOrderRepositoryInterface
{
    public function __construct(
        private AllianceOrderResource $resource,
        private AllianceOrderInterfaceFactory $allianceOrderFactory,
        private Logger $logger
    ) {}

    /**
     * @inheritDoc
     */
    public function save(AllianceOrderInterface $allianceOrder): AllianceOrderInterface
    {
        try {
            $this->resource->save($allianceOrder);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save order: %1', $e->getMessage()));
        }

        return $allianceOrder;
    }

    /**
     * @inheritDoc
     */
    public function get($id): AllianceOrderInterface
    {
        $allianceOrder = $this->allianceOrderFactory->create();
        $this->resource->load($allianceOrder, $id);
        if (!$allianceOrder->getId()) {
            $message = __('Alliance order with ID "%1" not found.', $id);
            $this->logger->error($message);
            throw new NoSuchEntityException($message);
        }

        return $allianceOrder;
    }



    /**
     * @inheritDoc
     */
    public function delete(AllianceOrderInterface $allianceOrder): bool
    {
        $this->resource->delete($allianceOrder);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getByOrderId(string $orderId): AllianceOrderInterface
    {
        $allianceOrder = $this->allianceOrderFactory->create();
        $this->resource->load($allianceOrder, $orderId, AllianceOrderInterface::ORDER_ID);
        if (!$allianceOrder->getOrderId()) {
            $message = __('Refund with order ID "%1" not found.', $orderId);
            $this->logger->error($message);
            throw new NoSuchEntityException($message);
        }

        return $allianceOrder;
    }

    /**
     * @inheritDoc
     */
    public function getByHppOrderId(string $hppOrderId): AllianceOrderInterface
    {
        $allianceOrder = $this->allianceOrderFactory->create();
        $this->resource->load($allianceOrder, $hppOrderId, AllianceOrderInterface::HPP_ORDER_ID);
        if (!$allianceOrder->getOrderId()) {
            $message = __('Refund with order ID "%1" not found.', $hppOrderId);
            $this->logger->error($message);
            throw new NoSuchEntityException($message);
        }

        return $allianceOrder;
    }
}
