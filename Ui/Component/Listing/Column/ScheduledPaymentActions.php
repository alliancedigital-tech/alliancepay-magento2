<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Ui\Component\Listing\Column;

use Alliance\AlliancePay\Api\Data\ScheduledPaymentInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * ScheduledPaymentActions.
 */
class ScheduledPaymentActions extends Column
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
            $id = $item[ScheduledPaymentInterface::ENTITY_ID];
            $item[$this->getData('name')] = [
                'run_now' => [
                    'href' => $this->urlBuilder->getUrl('alliance_pay/scheduledpayment/runnow', ['id' => $id]),
                    'label' => __('Run Now'),
                    'confirm' => [
                        'title' => __('Run Scheduled Payment Now'),
                        'message' => __('Are you sure you want to trigger this recurring payment now?'),
                    ],
                ],
            ];
        }

        return $dataSource;
    }
}
