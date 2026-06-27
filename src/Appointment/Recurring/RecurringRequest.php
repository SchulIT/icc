<?php

namespace App\Appointment\Recurring;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class RecurringRequest {
    #[Assert\NotNull]
    public DateTime|null $start = null;

    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'start')]
    public DateTime|null $end = null;
}
