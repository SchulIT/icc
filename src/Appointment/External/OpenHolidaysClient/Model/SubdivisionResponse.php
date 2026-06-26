<?php

namespace App\Appointment\External\OpenHolidaysClient\Model;

class SubdivisionResponse {
    public string $code;
    public string $isoCode;
    public string $shortName;

    /** @var LocalizedText[]  */
    public array $category;

    /** @var LocalizedText[] */
    public array $name;

    /** @var string[] */
    public array $officialLanguages;
}
