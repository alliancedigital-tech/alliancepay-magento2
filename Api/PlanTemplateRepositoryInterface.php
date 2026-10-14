<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

use Alliance\AlliancePay\Api\Data\PlanTemplateInterface;

/**
 * PlanTemplateRepositoryInterface.
 */
interface PlanTemplateRepositoryInterface
{
    /**
     * @param int $id
     * @return PlanTemplateInterface
     */
    public function get(int $id): PlanTemplateInterface;

    /**
     * @param string $code
     * @return PlanTemplateInterface
     */
    public function getByCode(string $code): PlanTemplateInterface;

    /**
     * @param PlanTemplateInterface $entity
     * @return bool
     */
    public function delete(PlanTemplateInterface $entity): bool;

    /**
     * @param PlanTemplateInterface $entity
     * @return PlanTemplateInterface
     */
    public function save(PlanTemplateInterface $entity): PlanTemplateInterface;
}
