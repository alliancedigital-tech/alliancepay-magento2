<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

/**
 * ScheduledPaymentGatewayClientInterface.
 */
interface ScheduledPaymentGatewayClientInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function createPlan(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function updatePlan(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function updatePlanStatus(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function updatePlanVersion(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function deleteVersion(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function createSubscriptionOrder(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function cancelSubscriptionOrder(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function triggerRecurringPayment(array $data): array;
}
