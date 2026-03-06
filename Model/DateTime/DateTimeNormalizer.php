<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\DateTime;

use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class DateTimeNormalizer.
 */
class DateTimeNormalizer
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private TimezoneInterface $timezone,
    ) {}

    /**
     * @throws Exception
     */
    public function formatCustomDate(string $inputDate): string
    {
        $cleanMilliseconds = preg_replace('/.\d{3}$/', '', $inputDate);
        $normalized = str_replace('.', '-', $cleanMilliseconds);
        $date = DateTime::createFromFormat(
            self::DATE_TIME_FORMAT,
            $normalized,
            new DateTimeZone($this->timezone->getConfigTimezone())
        );

        return $date->format(self::DATE_TIME_FORMAT);
    }
}
