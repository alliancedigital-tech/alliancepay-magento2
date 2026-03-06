<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Payment;

use Alliance\AlliancePay\Api\Data\RefundInterface;
use Alliance\AlliancePay\Api\Data\RefundInterfaceFactory;
use Alliance\AlliancePay\Api\GatewayClientInterface;
use Alliance\AlliancePay\Api\RefundRepositoryInterface;
use Alliance\AlliancePay\Exception\RefundException;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Alliance\AlliancePay\Model\DateTime\DateTimeNormalizer;
use Alliance\AlliancePay\Model\Refund;
use Alliance\AlliancePay\Service\ConvertData\ConvertDataService;
use Alliance\AlliancePay\Service\Payment\Service\ServiceAbstract;
use Magento\Framework\Api\DataObjectHelper;
use Exception;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class RefundService.
 */
class RefundService extends ServiceAbstract
{
    public const REFUND_DATA_FIELD_MERCHANT_REQUEST_ID = 'merchantRequestId';
    public const REFUND_DATA_FIELD_OPERATION_ID = 'operationId';
    public const REFUND_DATA_FIELD_MERCHANT_ID = 'merchantId';
    public const REFUND_DATA_FIELD_COIN_AMOUNT = 'coinAmount';
    public const REFUND_DATA_FIELD_NOTIFICATION_URL = 'notificationUrl';
    public const REFUND_DATA_FIELD_DATE = 'date';

    public function __construct(
        private GatewayClientInterface $gatewayClient,
        private AllianceConfig $allianceConfig,
        private RefundInterfaceFactory $refundFactory,
        private RefundRepositoryInterface $refundRepository,
        private ConvertDataService $convertDataService,
        private readonly DataObjectHelper $dataObjectHelper,
        private DateTime $dateTime,
        private DateTimeNormalizer $dateTimeNormalizer,
        private Logger $logger
    ) {}

    /**
     * @param string $operationId
     * @param float $amount
     * @param string $callbackUrl
     * @return array|true[]
     */
    public function refund(string $operationId, float $amount, string $callbackUrl): array
    {
        try {
            $refundData = $this->prepareRefundData(
                $operationId,
                $this->prepareCoinAmount((float) $amount),
                $callbackUrl
            );

            $refundData = $this->gatewayClient->refund($refundData);
            $convertedRefundData = $this->convertDataService->camelToSnakeArrayKeys($refundData);
            $convertedRefundData[RefundInterface::CREATION_DATE_TIME]
                = $this->dateTimeNormalizer->formatCustomDate(
                    $convertedRefundData[RefundInterface::CREATION_DATE_TIME]
            );
            $convertedRefundData[RefundInterface::MODIFICATION_DATE_TIME]
                = $this->dateTimeNormalizer->formatCustomDate(
                    $convertedRefundData[RefundInterface::MODIFICATION_DATE_TIME]
            );
            $refundEntity = $this->refundFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $refundEntity,
                $convertedRefundData,
                RefundInterface::class
            );
            $this->refundRepository->save($refundEntity);

            if (!isset($refundData[RefundInterface::TYPE])) {
                throw new RefundException(__('Invalid refund response from gateway'));
            }

            $result = [
                'success' => true,
            ];

            if ($refundEntity->getStatus() === Refund::REFUND_STATUS_SUCCESS
                || $refundEntity->getStatus() === Refund::REFUND_STATUS_PENDING
            ) {
                $result = [
                    'success' => true,
                    'transaction_id' => $refundEntity->getOperationId(),
                ];
            }
            if ($refundEntity->getStatus() === Refund::REFUND_STATUS_FAIL) {
                $result['success'] = false;
                $result['message'] = __('Refund service error.');
            }

            return $result;
        } catch (Exception $e) {
            $this->logger->error('Refund service error: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => __('Refund service error.')
        ];
    }

    /**
     * @param string $operationId
     * @param int $amount
     * @param string $callbackUrl
     * @return array
     */
    private function prepareRefundData(string $operationId, int $amount, string $callbackUrl): array
    {
        $preparedData = [];
        $preparedData[self::REFUND_DATA_FIELD_OPERATION_ID] = $operationId;
        $preparedData[self::REFUND_DATA_FIELD_COIN_AMOUNT] = $amount;
        $preparedData[self::REFUND_DATA_FIELD_MERCHANT_REQUEST_ID] = $this->generateMerchantRequestId();
        $preparedData[self::REFUND_DATA_FIELD_MERCHANT_ID] = $this->allianceConfig->getMerchantId();
        $preparedData[self::REFUND_DATA_FIELD_DATE] = $this->getRefundDate();
        $preparedData[self::REFUND_DATA_FIELD_NOTIFICATION_URL] = $callbackUrl;

        return $preparedData;
    }

    /**
     * @return string
     */
    private function getRefundDate(): string
    {
        $date = $this->dateTime->date('Y-m-d H:i:s.vP', $this->dateTime->timestamp());

        return preg_replace('/(\.\d{2})\d/', '$1',$date);
    }
}
