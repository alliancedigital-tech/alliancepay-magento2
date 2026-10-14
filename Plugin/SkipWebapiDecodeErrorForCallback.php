<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Plugin;

use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class SkipWebapiDecodeErrorForCallback.
 */
class SkipWebapiDecodeErrorForCallback
{
    private const CALLBACK_PATH = '/V1/alliance_pay/callback';

    /**
     * @param Request $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundGetBodyParams(Request $subject, callable $proceed): array
    {
        if (!str_contains((string)$subject->getPathInfo(), self::CALLBACK_PATH)) {
            return $proceed();
        }

        try {
            return $proceed();
        } catch (WebapiException $e) {
            return [];
        }
    }
}
