<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Ui\Component\Listing\Column;

use Alliance\AlliancePay\Api\Data\SubscriptionPlanInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * SubscriptionPlanActions.
 */
class SubscriptionPlanActions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $id = $item[SubscriptionPlanInterface::ENTITY_ID];
            $item[$this->getData('name')] = [
                'view' => [
                    'href' => $this->urlBuilder->getUrl('alliance_pay/subscriptionplan/edit', ['id' => $id]),
                    'label' => __('View'),
                ],
            ];
        }

        return $dataSource;
    }
}
