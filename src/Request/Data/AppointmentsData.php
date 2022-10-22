<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentsData {

    /**
     * @Serializer\Type("array<App\Request\Data\AppointmentData>")
     * @UniqueId(propertyPath="id")
     * @var AppointmentData[]
     */
    #[Assert\Valid]
    private array $appointments = [ ];

    /**
     * @return AppointmentData[]
     */
    public function getAppointments() {
        return $this->appointments;
    }

    /**
     * @param AppointmentData[] $appointments
     */
    public function setAppointments($appointments): AppointmentsData {
        $this->appointments = $appointments;
        return $this;
    }
}