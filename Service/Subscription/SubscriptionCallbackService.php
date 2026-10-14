<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Alliance\AlliancePay\Api\Data\ScheduledPaymentInterface;
use Alliance\AlliancePay\Api\Data\ScheduledPaymentInterfaceFactory;
use Alliance\AlliancePay\Api\Data\SubscriptionInterface;
use Alliance\AlliancePay\Api\ScheduledPaymentRepositoryInterface;
use Alliance\AlliancePay\Api\SubscriptionRepositoryInterface;
use Alliance\AlliancePay\Logger\Logger;
use Alliance\AlliancePay\Model\Config\AllianceConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * SubscriptionCallbackService.
 */
class SubscriptionCallbackService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly ScheduledPaymentRepositoryInterface $scheduledPaymentRepository,
        private readonly ScheduledPaymentInterfaceFactory $scheduledPaymentFactory,
        private readonly SubscriptionPlanCounterService $planCounterService,
        private readonly NonDestructiveMerger $merger,
        private readonly AuditLogger $auditLogger,
        private readonly AllianceConfig $allianceConfig,
        private readonly ResourceConnection $resourceConnection,
        private readonly DateTime $dateTime,
        private readonly Logger $logger
    ) {
    }

    /**
     * @param array $callbackData
     * @return void
     */
    public function processCallback(array $callbackData): void
    {
        $hppOrderId = $callbackData['hppOrderId'] ?? null;

        if (empty($hppOrderId)) {
            return;
        }

        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();

        try {
            $subscription = $this->subscriptionRepository->getByProviderOrderId((string)$hppOrderId);
            $subscription->setCallbackData($callbackData);
            $this->subscriptionRepository->save($subscription);

            $this->seedScheduledPayments($subscription, $callbackData);
            $this->applyDelivery($subscription, $callbackData);

            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            $this->logger->error('Subscription callback processing failed: ' . $exception->getMessage());
            $this->auditLogger->log('CALLBACK_ERROR', null, null, null, ['message' => $exception->getMessage()]);
        }
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param array $callbackData
     * @return void
     */
    private function seedScheduledPayments(SubscriptionInterface $subscription, array $callbackData): void
    {
        $scheduledPayments = (array)($callbackData['scheduledPayments'] ?? []);

        foreach ($scheduledPayments as $index => $entry) {
            $scheduledPaymentId = $entry['scheduledPaymentId'] ?? null;

            if ($scheduledPaymentId === null) {
                continue;
            }

            try {
                $this->scheduledPaymentRepository->getByScheduledPaymentId((string)$scheduledPaymentId);
                continue;
            } catch (NoSuchEntityException) {
            }

            $entity = $this->scheduledPaymentFactory->create();
            $entity->setSubscriptionId($subscription->getEntityId());
            $entity->setProviderOrderId((string)$subscription->getProviderOrderId());
            $entity->setScheduledPaymentId((string)$scheduledPaymentId);
            $entity->setPaymentNumber((int)$index + 1);
            $entity->setStatus((string)($entry['status'] ?? ScheduledPaymentInterface::STATUS_WAITING));
            $entity->setScheduledDate($entry['scheduledDate'] ?? null);
            $entity->setRetryCount(0);
            $entity->setCreatedAt($this->dateTime->gmtDate());
            $this->scheduledPaymentRepository->save($entity);
        }
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param array $callbackData
     * @return void
     */
    private function applyDelivery(SubscriptionInterface $subscription, array $callbackData): void
    {
        $operations = $this->extractOperations($callbackData);
        $primaryOperation = $operations[0] ?? [];
        $operationId = isset($primaryOperation['operationId']) ? (string)$primaryOperation['operationId'] : null;
        $scheduledPaymentId = isset($callbackData['scheduledPaymentId'])
            ? (string)$callbackData['scheduledPaymentId']
            : null;

        $scheduledPayment = $this->resolveScheduledPayment($subscription, $scheduledPaymentId);

        $isDuplicateOperation = $operationId !== null
            && $operationId === $scheduledPayment->getOperationId();

        $incoming = [
            ScheduledPaymentInterface::STATUS => $callbackData['orderStatus'] ?? null,
            ScheduledPaymentInterface::OPERATION_ID => $operationId,
            ScheduledPaymentInterface::EXECUTED_AT => !empty($primaryOperation['status'])
                && $primaryOperation['status'] === ScheduledPaymentInterface::STATUS_SUCCESS
                ? $this->dateTime->gmtDate()
                : null,
        ];

        $merged = $this->merger->merge($scheduledPayment->getData(), $incoming);
        $scheduledPayment->addData($merged);

        if (!$isDuplicateOperation) {
            $this->transitionStatus($subscription, $scheduledPayment, (string)($callbackData['orderStatus'] ?? ''));
        }

        $this->reconcileAmount($subscription, $scheduledPayment, $primaryOperation);
        $this->scheduledPaymentRepository->save($scheduledPayment);
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param string|null $scheduledPaymentId
     * @return ScheduledPaymentInterface
     */
    private function resolveScheduledPayment(
        SubscriptionInterface $subscription,
        ?string $scheduledPaymentId
    ): ScheduledPaymentInterface {
        if ($scheduledPaymentId !== null) {
            try {
                return $this->scheduledPaymentRepository->getByScheduledPaymentId($scheduledPaymentId);
            } catch (NoSuchEntityException) {
            }
        }

        $entity = $this->scheduledPaymentFactory->create();
        $entity->setSubscriptionId($subscription->getEntityId());
        $entity->setProviderOrderId((string)$subscription->getProviderOrderId());
        $entity->setScheduledPaymentId($scheduledPaymentId);
        $entity->setStatus(ScheduledPaymentInterface::STATUS_WAITING);
        $entity->setRetryCount(0);
        $entity->setCreatedAt($this->dateTime->gmtDate());

        return $entity;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param ScheduledPaymentInterface $scheduledPayment
     * @param string $status
     * @return void
     */
    private function transitionStatus(
        SubscriptionInterface $subscription,
        ScheduledPaymentInterface $scheduledPayment,
        string $status
    ): void {
        match ($status) {
            ScheduledPaymentInterface::STATUS_WAITING, ScheduledPaymentInterface::STATUS_PROCESSING => null,
            ScheduledPaymentInterface::STATUS_SUCCESS => $this->handleSuccess($subscription, $scheduledPayment),
            ScheduledPaymentInterface::STATUS_FAIL => null,
            ScheduledPaymentInterface::STATUS_RETRY => $scheduledPayment->setRetryCount(
                $scheduledPayment->getRetryCount() + 1
            ),
            ScheduledPaymentInterface::STATUS_SKIPPED_PAUSED => null,
            ScheduledPaymentInterface::STATUS_INVALIDATED => $this->auditLogger->log(
                'SCHEDULED_PAYMENT_INVALIDATED',
                $subscription->getPlanId(),
                null,
                null,
                ['subscriptionId' => $subscription->getEntityId()]
            ),
            default => $this->logger->warning('Unknown scheduled payment status received: ' . $status),
        };
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param ScheduledPaymentInterface $scheduledPayment
     * @return void
     */
    private function handleSuccess(SubscriptionInterface $subscription, ScheduledPaymentInterface $scheduledPayment): void
    {
        if ($subscription->getStatus() === SubscriptionInterface::STATUS_PENDING) {
            $subscription->setStatus(SubscriptionInterface::STATUS_ACTIVE);
            $this->subscriptionRepository->save($subscription);
            $this->planCounterService->increment((int)$subscription->getPlanId());
        }
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param ScheduledPaymentInterface $scheduledPayment
     * @param array $operation
     * @return void
     */
    private function reconcileAmount(
        SubscriptionInterface $subscription,
        ScheduledPaymentInterface $scheduledPayment,
        array $operation
    ): void {
        if (empty($operation)) {
            return;
        }

        $amount = $this->extractByPath($operation, (string)$this->allianceConfig->getRecurringAmountFieldPath());
        $paymentNumber = $scheduledPayment->getPaymentNumber();

        if ($amount === null || $paymentNumber === null) {
            $scheduledPayment->setAmountReconciliationStatus(ScheduledPaymentInterface::RECONCILIATION_STATUS_SKIPPED);

            return;
        }

        $rule = $this->findRuleForPaymentNumber($subscription->getRulesSnapshot(), $paymentNumber);

        if ($rule === null) {
            $scheduledPayment->setAmountReconciliationStatus(ScheduledPaymentInterface::RECONCILIATION_STATUS_SKIPPED);

            return;
        }

        if ((int)$rule['coinAmount'] !== (int)$amount) {
            $scheduledPayment->setAmountReconciliationStatus(ScheduledPaymentInterface::RECONCILIATION_STATUS_MISMATCH);
            $scheduledPayment->setReconciliationNote(
                sprintf('Expected %s, got %s', $rule['coinAmount'], $amount)
            );

            return;
        }

        $scheduledPayment->setAmountReconciliationStatus(ScheduledPaymentInterface::RECONCILIATION_STATUS_OK);
    }

    /**
     * @param array $rules
     * @param int $paymentNumber
     * @return array|null
     */
    private function findRuleForPaymentNumber(array $rules, int $paymentNumber): ?array
    {
        foreach ($rules as $rule) {
            $from = (int)($rule['paymentNumberFrom'] ?? 0);
            $to = (int)($rule['paymentNumberTo'] ?? 0);

            if ($paymentNumber >= $from && $paymentNumber <= $to) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * @param array $data
     * @param string $path
     * @return string|null
     */
    private function extractByPath(array $data, string $path): ?string
    {
        $segments = explode('.', $path);
        $value = $data;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }

            $value = $value[$segment];
        }

        return is_scalar($value) ? (string)$value : null;
    }

    /**
     * @param array $callbackData
     * @return array
     */
    private function extractOperations(array $callbackData): array
    {
        return match (true) {
            isset($callbackData['operations']) => (array)$callbackData['operations'],
            isset($callbackData['operation']) => [$callbackData['operation']],
            default => [],
        };
    }
}
