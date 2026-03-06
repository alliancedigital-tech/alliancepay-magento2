<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Model\Config\CountryCode;

use League\ISO3166\ISO3166;

/**
 * Class CountryCodeProvider.
 */
class CountryCodeProvider
{
    public function __construct(
        private readonly ISO3166 $countryCode
    ) {}

    /**
     * @param string $alpha2
     * @return string
     */
    public function getCountryNumericCodeByAlpha2(string $alpha2): string
    {
        $countryData = $this->countryCode->alpha2($alpha2);

        return $countryData['numeric'] ?? '';
    }
}
