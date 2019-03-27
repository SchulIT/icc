<?php

namespace App\Export;

use App\Entity\Appointment;
use App\Entity\User;
use App\Ics\IcsHelper;
use App\Ics\IcsItem;
use App\Repository\AppointmentRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class AppointmentIcsExporter {
    private $appointmentsRepository;
    private $icsHelper;

    public function __construct(AppointmentRepositoryInterface $appointmentsRepository, IcsHelper $icsHelper) {
        $this->appointmentsRepository = $appointmentsRepository;
        $this->icsHelper = $icsHelper;
    }

    /**
     * @param User $user
     * @return IcsItem[]
     */
    private function getIcsItems(User $user) {

    }

    /**
     * @param Appointment $appointment
     * @return IcsItem
     */
    private function makeIcsItem(Appointment $appointment): IcsItem {
        return (new IcsItem())
            ->setId(sprintf('appointment-%d', $appointment->getId()))
            ->setStart($appointment->getStart())
            ->setEnd($appointment->getEnd())
            ->setIsAllday($appointment->isAllDay())
            ->setSummary($appointment->getSubject())
            ->setDescription($this->makeDescription($appointment));
    }

    private function makeDescription(Appointment $appointment): string {

    }

    public function getIcsResponse(User $user): Response {

    }

}