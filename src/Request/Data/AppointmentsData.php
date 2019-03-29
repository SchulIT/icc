<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentsData {

    /**
     * @Serializer\Type("array<AppointmentData>")
     * @Assert\Valid()
     * @var AppointmentData[]
     */
    private $appointments;

    /**
     * @return AppointmentData[]
     */
    public function getAppointments(): array {
        return $this->appointments;
    }

    /**
     * @param AppointmentData[] $appointments
     * @return AppointmentsData
     */
    public function setAppointments(array $appointments): AppointmentsData {
        $this->appointments = $appointments;
        return $this;
    }
}