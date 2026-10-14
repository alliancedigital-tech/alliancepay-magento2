<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionInterface;
use Alliance\AlliancePay\Api\Data\SubscriptionInterfaceFactory;
use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * SubscriptionRepository.
 */
class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function __construct(
        private readonly SubscriptionResource $resource,
        private readonly SubscriptionInterfaceFactory $subscriptionFactory,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param SubscriptionInterface $entity
     * @return SubscriptionInterface
     * @throws CouldNotSaveException
     */
    public function save(SubscriptionInterface $entity): SubscriptionInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save subscription: %1', $e->getMessage()));
        }

        return $entity;
    }

    /**
     * @param int $id
     * @return SubscriptionInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): SubscriptionInterface
    {
        $entity = $this->subscriptionFactory->create();
        $this->resource->load($entity, $id);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(__('Subscription with ID "%1" not found.', $id));
        }

        return $entity;
    }

    /**
     * @param string $providerOrderId
     * @return SubscriptionInterface
     * @throws NoSuchEntityException
     */
    public function getByProviderOrderId(string $providerOrderId): SubscriptionInterface
    {
        $entity = $this->subscriptionFactory->create();
        $this->resource->load($entity, $providerOrderId, SubscriptionInterface::PROVIDER_ORDER_ID);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(
                __('Subscription with provider order ID "%1" not found.', $providerOrderId)
            );
        }

        return $entity;
    }

    /**
     * @param SubscriptionInterface $entity
     * @return bool
     */
    public function delete(SubscriptionInterface $entity): bool
    {
        $this->resource->delete($entity);

        return true;
    }
}
