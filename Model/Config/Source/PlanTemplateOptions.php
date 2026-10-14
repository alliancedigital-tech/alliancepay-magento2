<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config\Source;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterface;
use Alliance\AlliancePay\Model\ResourceModel\PlanTemplate\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * PlanTemplateOptions.
 */
class PlanTemplateOptions implements OptionSourceInterface
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(PlanTemplateInterface::STATUS, PlanTemplateInterface::STATUS_ACTIVE);

        $options = [];

        foreach ($collection as $template) {
            $options[] = [
                'value' => $template->getEntityId(),
                'label' => $template->getCode() . ' — ' . $template->getName(),
            ];
        }

        return $options;
    }
}
