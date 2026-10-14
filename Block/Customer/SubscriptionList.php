<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Block\Customer;

use Alliance\AlliancePay\Model\ResourceModel\Subscription\Collection;
use Alliance\AlliancePay\Model\ResourceModel\Subscription\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * SubscriptionList.
 */
class SubscriptionList extends Template
{
    public function __construct(
        Context $context,
        private readonly Session $customerSession,
        private readonly CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return Collection
     */
    public function getSubscriptions(): Collection
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', (int)$this->customerSession->getCustomerId());

        return $collection;
    }

    /**
     * @param int $subscriptionId
     * @return string
     */
    public function getCancelUrl(int $subscriptionId): string
    {
        return $this->getUrl('alliance_pay/subscription/cancel', ['id' => $subscriptionId]);
    }
}
