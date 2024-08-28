<?php

namespace App\Export\Untis\Timetable;

use App\Entity\TimetableWeek;
use Symfony\Component\Validator\Constraints as Assert;

class Week {
    #[Assert\NotNull]
    public ?TimetableWeek $week;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $untisWeek;
}