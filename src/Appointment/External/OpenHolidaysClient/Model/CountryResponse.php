<?php

namespace App\Appointment\External\OpenHolidaysClient\Model;

class CountryResponse {
    public string $isoCode;

    /** @var LocalizedText[] */
    public array $name;

    /** @var string[] */
    public array $officialLanguages;
}
