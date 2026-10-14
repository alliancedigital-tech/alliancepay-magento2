<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Service\Subscription;

use Magento\Framework\Exception\LocalizedException;

/**
 * RulesValidator.
 */
class RulesValidator
{
    private const REQUIRED_FIELDS = [
        'paymentNumberFrom',
        'paymentNumberTo',
        'intervalValue',
        'intervalUnit',
        'coinAmount',
        'currency',
    ];

    /**
     * @param array $rules
     * @return void
     * @throws LocalizedException
     */
    public function validate(array $rules): void
    {
        if (!$rules) {
            throw new LocalizedException(__('At least one rule is required.'));
        }

        foreach ($rules as $index => $rule) {
            if (!is_array($rule)) {
                throw new LocalizedException(__('Rule #%1 is invalid.', $index + 1));
            }

            foreach (self::REQUIRED_FIELDS as $field) {
                if (!isset($rule[$field]) || $rule[$field] === '') {
                    throw new LocalizedException(__('Rule #%1: "%2" is required.', $index + 1, $field));
                }
            }
        }
    }
}
