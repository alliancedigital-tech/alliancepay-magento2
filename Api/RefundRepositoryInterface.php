<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

use Alliance\AlliancePay\Api\Data\RefundInterface;

/**
 * RefundRepositoryInterface.
 */
interface RefundRepositoryInterface
{
    /**
     * @param int $id
     * @return RefundInterface
     */
    public function get(int $id) : RefundInterface;

    /**
     * @param string $orderId
     * @return RefundInterface
     */
    public function getByOrderId(string $orderId) : RefundInterface;

    /**
     * @param RefundInterface $refund
     * @return bool
     */
    public function delete(RefundInterface $refund) : bool;

    /**
     * @param RefundInterface $refund
     * @return RefundInterface
     */
    public function save(RefundInterface $refund) : RefundInterface;
}
