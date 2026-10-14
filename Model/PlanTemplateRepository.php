<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterface;
use Alliance\AlliancePay\Api\Data\PlanTemplateInterfaceFactory;
use Alliance\AlliancePay\Api\PlanTemplateRepositoryInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\ResourceModel\PlanTemplateResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * PlanTemplateRepository.
 */
class PlanTemplateRepository implements PlanTemplateRepositoryInterface
{
    public function __construct(
        private readonly PlanTemplateResource $resource,
        private readonly PlanTemplateInterfaceFactory $planTemplateFactory,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param PlanTemplateInterface $entity
     * @return PlanTemplateInterface
     * @throws CouldNotSaveException
     */
    public function save(PlanTemplateInterface $entity): PlanTemplateInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save plan template: %1', $e->getMessage()));
        }

        return $entity;
    }

    /**
     * @param int $id
     * @return PlanTemplateInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): PlanTemplateInterface
    {
        $entity = $this->planTemplateFactory->create();
        $this->resource->load($entity, $id);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(__('Plan template with ID "%1" not found.', $id));
        }

        return $entity;
    }

    /**
     * @param string $code
     * @return PlanTemplateInterface
     * @throws NoSuchEntityException
     */
    public function getByCode(string $code): PlanTemplateInterface
    {
        $entity = $this->planTemplateFactory->create();
        $this->resource->load($entity, $code, PlanTemplateInterface::CODE);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(__('Plan template with code "%1" not found.', $code));
        }

        return $entity;
    }

    /**
     * @param PlanTemplateInterface $entity
     * @return bool
     */
    public function delete(PlanTemplateInterface $entity): bool
    {
        $this->resource->delete($entity);

        return true;
    }
}
