<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Ui\Component\Listing\Column;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * PlanTemplateActions.
 */
class PlanTemplateActions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        private readonly Escaper $escaper,
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
            $id = $item[PlanTemplateInterface::ENTITY_ID];
            $item[$this->getData('name')] = [
                'edit' => [
                    'href' => $this->urlBuilder->getUrl('alliance_pay/plantemplate/edit', ['id' => $id]),
                    'label' => __('Edit'),
                ],
                'delete' => [
                    'href' => $this->urlBuilder->getUrl('alliance_pay/plantemplate/delete', ['id' => $id]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete Plan Template'),
                        'message' => __('Are you sure you want to delete "%1"?', $this->escaper->escapeJs($item['name'] ?? '')),
                    ],
                ],
            ];
        }

        return $dataSource;
    }
}
