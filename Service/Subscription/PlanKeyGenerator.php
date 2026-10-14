<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

/**
 * PlanKeyGenerator.
 */
class PlanKeyGenerator
{
    /**
     * @param string $merchantId
     * @param int $productId
     * @param int|null $templateId
     * @param string $categoryIndicator
     * @param array $rules
     * @return string
     */
    public function generate(
        string $merchantId,
        int $productId,
        ?int $templateId,
        string $categoryIndicator,
        array $rules
    ): string {
        $normalizedRules = $this->normalizeRules($rules);

        return hash(
            'sha256',
            implode('|', [
                $merchantId,
                (string)$productId,
                (string)($templateId ?? 0),
                $categoryIndicator,
                json_encode($normalizedRules),
            ])
        );
    }

    /**
     * @param array $rules
     * @return array
     */
    private function normalizeRules(array $rules): array
    {
        usort(
            $rules,
            static fn (array $a, array $b): int => ($a['paymentNumberFrom'] ?? 0) <=> ($b['paymentNumberFrom'] ?? 0)
        );

        return array_map(
            static function (array $rule): array {
                ksort($rule);

                return array_map(
                    static fn ($value): string => is_scalar($value) ? (string)$value : json_encode($value),
                    $rule
                );
            },
            $rules
        );
    }
}
