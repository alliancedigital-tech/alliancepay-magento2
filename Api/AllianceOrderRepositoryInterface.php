<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

use Alliance\AlliancePay\Api\Data\AllianceOrderInterface;

/**
 * AllianceOrderRepositoryInterface.
 */
interface AllianceOrderRepositoryInterface
{
    /**
     * @param int $id
     * @return AllianceOrderInterface
     */
    public function get(int $id) : AllianceOrderInterface;

    /**
     * @param string $hppOrderId
     * @return AllianceOrderInterface
     */
    public function getByHppOrderId(string $hppOrderId) : AllianceOrderInterface;

    /**
     * @param string $orderId
     * @return AllianceOrderInterface
     */
    public function getByOrderId(string $orderId) : AllianceOrderInterface;

    /**
     * @param AllianceOrderInterface $entity
     * @return bool
     */
    public function delete(AllianceOrderInterface $entity) : bool;

    /**
     * @param AllianceOrderInterface $entity
     * @return AllianceOrderInterface
     */
    public function save(AllianceOrderInterface $entity) : AllianceOrderInterface;
}
