<?php

namespace App\Tools\TimetableGpuExport;

use App\Timetable\Entity\TimetableWeek;
use Symfony\Component\Validator\Constraints as Assert;

class Week {
    #[Assert\NotNull]
    public ?TimetableWeek $week;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $untisWeek;
}