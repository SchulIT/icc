<?php

namespace App\Appointment\External\OpenHolidaysClient\Model;

use DateTime;

class HolidayResponse {
    public string $id;
    /** @var LocalizedText[] */
    public array $name;
    public bool $nationwide = false;
    public RegionalScope $regionalScope;
    public DateTime $startDate;
    public DateTime $endDate;
    public HolidayType $type;
}
