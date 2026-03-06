<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Api;

/**
 * CallbackHandlerInterface.
 */
interface CallbackHandlerInterface
{
    /**
     * @return void
     */
    public function processCallback(): void;
}
