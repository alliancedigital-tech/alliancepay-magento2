<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Block\Adminhtml\SubscriptionPlan;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * View.
 */
class View extends Template
{
    public function __construct(
        Context $context,
        private readonly Registry $coreRegistry,
        private readonly SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return SubscriptionPlanInterface
     */
    public function getPlan(): SubscriptionPlanInterface
    {
        return $this->coreRegistry->registry('alliance_subscription_plan');
    }

    /**
     * @return string
     */
    public function getRulesJson(): string
    {
        return $this->serializer->serialize($this->getPlan()->getRules());
    }

    /**
     * @return string
     */
    public function getArchiveUrl(): string
    {
        return $this->getUrl('alliance_pay/subscriptionplan/archive', ['id' => $this->getPlan()->getEntityId()]);
    }
}
