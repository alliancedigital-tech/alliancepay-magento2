<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Block\Adminhtml\Order\View;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;
use Alliance\AlliancePay\Api\Data\AllianceOrderInterfaceFactory;
use Alliance\AlliancePay\Model\Payment\AlliancePayment;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Alliance\AlliancePay\Api\AllianceOrderRepositoryInterface;
use Alliance\AlliancePay\Service\ConvertData\ConvertDataService;
use Magento\Framework\Serialize\SerializerInterface;
use Alliance\AlliancePay\Model\DateTime\DateTimeNormalizer;
use Magento\Sales\Api\Data\OrderInterface;


/**
 * Class PaymentStatus.
 */
class PaymentStatus extends Template
{
    private AllianceOrderInterface $allianceOrder;
    private OrderInterface $order;
    public function __construct(
        Context $context,
        private Registry $coreRegistry,
        private AllianceOrderRepositoryInterface $allianceOrderRepository,
        private AllianceOrderInterfaceFactory $allianceOrderFactory,
        private SerializerInterface $serializer,
        private readonly ConvertDataService $convertDataService,
        private readonly DateTimeNormalizer $dateTimeNormalizer,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function getPaymentStatus()
    {
        $this->initOrderData();

        return $this->allianceOrder->getOrderStatus();
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        $this->initOrderData();

        return $this->order->getId();
    }

    public function getAllianceOrderStatus()
    {
        $this->initOrderData();

        return $this->allianceOrder->getOrderStatus();
    }

    /**
     * @return array
     */
    public function getOperations(): array
    {
        $this->initOrderData();
        $operationsData = [];
        $preparedOperations = [];

        if ($this->allianceOrder->getIsCallbackReturned()) {
            $callBackData = $this->serializer->unserialize($this->allianceOrder->getCallbackData());
            if (!empty($callBackData['operations'])) {
                $operationsData = $callBackData['operations'];
            } elseif (!empty($callBackData['operation'])) {
                $operationsData[] = $callBackData['operation'];
            }

            if (!empty($operationsData)) {
                foreach ($operationsData as $operationIndex => $operationData) {
                    $convertedOperations = $this->convertDataService->camelToSnakeArrayKeys($operationData);
                    $creationDateTime = $convertedOperations[AllianceOrderInterface::OPERATION_CREATION_TIME] ?? '';
                    if (!empty($creationDateTime)) {
                        $preparedOperations[$operationIndex][AllianceOrderInterface::OPERATION_CREATION_TIME] =
                            $this->dateTimeNormalizer->formatCustomDate($creationDateTime);
                    } else {
                        $preparedOperations[$operationIndex][AllianceOrderInterface::OPERATION_STATUS] = '';
                    }
                    $preparedOperations[$operationIndex][AllianceOrderInterface::OPERATION_ID] =
                        $convertedOperations[AllianceOrderInterface::OPERATION_ID] ?? '';
                    $preparedOperations[$operationIndex][AllianceOrderInterface::OPERATION_STATUS] =
                        $convertedOperations[AllianceOrderInterface::OPERATION_STATUS] ?? '';
                    $preparedOperations[$operationIndex][AllianceOrderInterface::OPERATION_TYPE] =
                        $convertedOperations[AllianceOrderInterface::OPERATION_TYPE] ?? '';
                }
            }
        }

        return $preparedOperations;
    }

    /**
     * @return bool
     */
    public function canCheckStatus()
    {
        $this->initOrderData();

        return $this->order->getPayment()->getMethod() === AlliancePayment::PAYMENT_METHOD_CODE;
    }

    /**
     * @return string
     */
    public function getCheckStatusUrl()
    {
        $this->initOrderData();

        return $this->getUrl('alliance_pay/order/checkPaymentStatus', [
            'hpp_order_id' => $this->allianceOrder->getHppOrderId()
        ]);
    }

    /**
     * @return void
     */
    private function initOrderData(): void
    {
        if (empty($this->order)) {
            $this->order = $this->coreRegistry->registry('current_order');
        }

        try {
            if (empty($this->allianceOrder) && !empty($this->order)) {
                $this->allianceOrder = $this->allianceOrderRepository->getByOrderId($this->order->getId());
            }
        } catch (NoSuchEntityException) {
            $this->allianceOrder = $this->allianceOrderFactory->create();
        }
    }
}
