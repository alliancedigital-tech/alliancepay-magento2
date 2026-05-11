<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

/**
 * CustomerDataValidatorInterface.
 */
interface CustomerDataValidatorInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function validate(array $data): array;
}
