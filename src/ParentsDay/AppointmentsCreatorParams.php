<?php

namespace App\ParentsDay;

use App\Entity\ParentsDay;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentsCreatorParams {

    #[Assert\NotNull]
    public ?ParentsDay $parentsDay;

    #[Assert\GreaterThan(0)]
    public int $duration = 10;

    #[Assert\NotBlank]
    #[Assert\Time]
    public ?string $from = null;

    #[Assert\NotBlank]
    #[Assert\Time]
    #[Assert\GreaterThan(propertyPath: 'from')]
    public ?string $until = null;

    public bool $removeExistingAppointments = true;
}