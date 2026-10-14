<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

/**
 * NonDestructiveMerger.
 */
class NonDestructiveMerger
{
    /**
     * @param array $currentValues
     * @param array $incomingValues
     * @return array
     */
    public function merge(array $currentValues, array $incomingValues): array
    {
        $merged = $currentValues;

        foreach ($incomingValues as $field => $value) {
            if ($value !== null && $value !== '') {
                $merged[$field] = $value;
            }
        }

        return $merged;
    }
}
