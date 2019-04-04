<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentCategoriesData {

    /**
     * @Serializer\Type("array<App\Request\Data\AppointmentCategoryData>")
     * @Assert\Valid()
     * @var AppointmentCategoryData[]
     */
    private $appointments;

    /**
     * @return mixed
     */
    public function getAppointments() {
        return $this->appointments;
    }

    /**
     * @param mixed $appointments
     * @return AppointmentCategoriesData
     */
    public function setAppointments($appointments) {
        $this->appointments = $appointments;
        return $this;
    }
}