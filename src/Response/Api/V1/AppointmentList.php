<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class AppointmentList {

    /**
     * @Serializer\Type("array<App\Response\Api\V1\Appointment>")
     * @Serializer\SerializedName("appointments")
     * @var Appointment[]
     */
    private $appointments;

    /**
     * @return Appointment[]
     */
    public function getAppointments(): array {
        return $this->appointments;
    }

    /**
     * @param Appointment[] $appointments
     * @return AppointmentList
     */
    public function setAppointments(array $appointments): AppointmentList {
        $this->appointments = $appointments;
        return $this;
    }
}