<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentsData {

    /**
     * @Serializer\Type("array<App\Request\Data\AppointmentData>")
     * @Assert\Valid()
     * @var AppointmentData[]
     */
    private $appointments = [ ];

    /**
     * @return AppointmentData[]
     */
    public function getAppointments() {
        return $this->appointments;
    }

    /**
     * @param AppointmentData[] $appointments
     * @return AppointmentsData
     */
    public function setAppointments($appointments): AppointmentsData {
        $this->appointments = $appointments;
        return $this;
    }
}