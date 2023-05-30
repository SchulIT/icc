<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class AppointmentList {

    /**
     * @var Appointment[]
     */
    #[Serializer\Type('array<App\Response\Api\V1\Appointment>')]
    #[Serializer\SerializedName('appointments')]
    private ?array $appointments = null;

    /**
     * @return Appointment[]
     */
    public function getAppointments(): array {
        return $this->appointments;
    }

    /**
     * @param Appointment[] $appointments
     */
    public function setAppointments(array $appointments): AppointmentList {
        $this->appointments = $appointments;
        return $this;
    }
}