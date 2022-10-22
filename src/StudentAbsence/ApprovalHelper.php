<?php

namespace App\StudentAbsence;

use App\Entity\StudentAbsence;
use App\Entity\User;
use App\Event\StudentAbsenceApprovalChangedEvent;
use App\Repository\StudentAbsenceRepositoryInterface;
use DateTime;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ApprovalHelper {
    public function __construct(private StudentAbsenceRepositoryInterface $repository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function setApprovalStatus(StudentAbsence $absence, bool $isApproved, User $user): void {
        if($absence->getType()->isMustApprove() === false) {
            return;
        }

        $absence->setApprovedBy($user);
        $absence->setApprovedAt(new DateTime());
        $absence->setIsApproved($isApproved);

        $this->repository->persist($absence);
        $this->eventDispatcher->dispatch(new StudentAbsenceApprovalChangedEvent($absence));
    }
}