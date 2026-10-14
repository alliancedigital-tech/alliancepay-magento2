<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterfaceFactory;
use Alliance\AlliancePay\Api\SubscriptionPlanRepositoryInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlan\CollectionFactory;
use Alliance\AlliancePay\Model\ResourceModel\SubscriptionPlanResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * SubscriptionPlanRepository.
 */
class SubscriptionPlanRepository implements SubscriptionPlanRepositoryInterface
{
    public function __construct(
        private readonly SubscriptionPlanResource $resource,
        private readonly SubscriptionPlanInterfaceFactory $subscriptionPlanFactory,
        private readonly ResourceConnection $resourceConnection,
        private readonly CollectionFactory $collectionFactory,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param SubscriptionPlanInterface $entity
     * @return SubscriptionPlanInterface
     * @throws CouldNotSaveException
     */
    public function save(SubscriptionPlanInterface $entity): SubscriptionPlanInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save subscription plan: %1', $e->getMessage()));
        }

        return $entity;
    }

    /**
     * @param int $id
     * @return SubscriptionPlanInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): SubscriptionPlanInterface
    {
        $entity = $this->subscriptionPlanFactory->create();
        $this->resource->load($entity, $id);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(__('Subscription plan with ID "%1" not found.', $id));
        }

        return $entity;
    }

    /**
     * @param string $planKey
     * @return SubscriptionPlanInterface
     * @throws NoSuchEntityException
     */
    public function getByPlanKey(string $planKey): SubscriptionPlanInterface
    {
        $entity = $this->subscriptionPlanFactory->create();
        $this->resource->load($entity, $planKey, SubscriptionPlanInterface::PLAN_KEY);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(__('Subscription plan with plan key "%1" not found.', $planKey));
        }

        return $entity;
    }

    /**
     * @param string $providerPlanId
     * @return SubscriptionPlanInterface
     * @throws NoSuchEntityException
     */
    public function getByProviderPlanId(string $providerPlanId): SubscriptionPlanInterface
    {
        $entity = $this->subscriptionPlanFactory->create();
        $this->resource->load($entity, $providerPlanId, SubscriptionPlanInterface::PROVIDER_PLAN_ID);

        if (!$entity->getEntityId()) {
            throw new NoSuchEntityException(
                __('Subscription plan with provider plan ID "%1" not found.', $providerPlanId)
            );
        }

        return $entity;
    }

    /**
     * @param int $productId
     * @return SubscriptionPlanInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getActiveByProductId(int $productId): SubscriptionPlanInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resource->getMainTable(), 'entity_id')
            ->where('product_id = ?', $productId)
            ->where('is_active = ?', 1)
            ->limit(1);
        $entityId = $connection->fetchOne($select);

        if (!$entityId) {
            throw new NoSuchEntityException(
                __('No active subscription plan found for product ID "%1".', $productId)
            );
        }

        return $this->get((int)$entityId);
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getActiveListByProductId(int $productId): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SubscriptionPlanInterface::PRODUCT_ID, $productId);
        $collection->addFieldToFilter(SubscriptionPlanInterface::IS_ACTIVE, 1);

        return array_values($collection->getItems());
    }

    /**
     * @param SubscriptionPlanInterface $entity
     * @return bool
     * @throws \Exception
     */
    public function delete(SubscriptionPlanInterface $entity): bool
    {
        $this->resource->delete($entity);

        return true;
    }
}
