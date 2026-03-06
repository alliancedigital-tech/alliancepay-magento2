<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

/**
 * ConvertDataServiceInterface.
 */
interface ConvertDataServiceInterface
{
    public function camelToSnakeArrayKeys(array $data): array;
}
