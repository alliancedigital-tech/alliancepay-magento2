<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\ScheduledPaymentInterface;
use Alliance\AlliancePay\Api\Data\ScheduledPaymentInterfaceFactory;
use Alliance\AlliancePay\Api\ScheduledPaymentRepositoryInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\ResourceModel\ScheduledPaymentResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * ScheduledPaymentRepository.
 */
class ScheduledPaymentRepository implements ScheduledPaymentRepositoryInterface
{
    public function __construct(
        private readonly ScheduledPaymentResource $resource,
        private readonly ScheduledPaymentInterfaceFactory $scheduledPaymentFactory,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param ScheduledPaymentInterface $entity
     * @return ScheduledPaymentInterface
     * @throws CouldNotSaveException
     */
    public function save(ScheduledPaymentInterface $entity): ScheduledPaymentInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save scheduled payment: %1', $e->getMessage()));
        }

        return $entity;
    }

    /**
     * @param int $id
     * @return ScheduledPaymentInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): ScheduledPaymentInterface
    {
        $entity = $this->scheduledPaymentFactory->create();
        $this->resource->load($entity, $id);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(__('Scheduled payment with ID "%1" not found.', $id));
        }

        return $entity;
    }

    /**
     * @param string $operationId
     * @return ScheduledPaymentInterface
     * @throws NoSuchEntityException
     */
    public function getByOperationId(string $operationId): ScheduledPaymentInterface
    {
        $entity = $this->scheduledPaymentFactory->create();
        $this->resource->load($entity, $operationId, ScheduledPaymentInterface::OPERATION_ID);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(
                __('Scheduled payment with operation ID "%1" not found.', $operationId)
            );
        }

        return $entity;
    }

    /**
     * @param string $scheduledPaymentId
     * @return ScheduledPaymentInterface
     * @throws NoSuchEntityException
     */
    public function getByScheduledPaymentId(string $scheduledPaymentId): ScheduledPaymentInterface
    {
        $entity = $this->scheduledPaymentFactory->create();
        $this->resource->load($entity, $scheduledPaymentId, ScheduledPaymentInterface::SCHEDULED_PAYMENT_ID);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(
                __('Scheduled payment with scheduled payment ID "%1" not found.', $scheduledPaymentId)
            );
        }

        return $entity;
    }

    /**
     * @param ScheduledPaymentInterface $entity
     * @return bool
     */
    public function delete(ScheduledPaymentInterface $entity): bool
    {
        $this->resource->delete($entity);

        return true;
    }
}
