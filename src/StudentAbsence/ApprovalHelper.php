<?php

namespace App\StudentAbsence;

use App\StudentAbsence\Entity\StudentAbsence;
use App\Common\Entity\User;
use App\StudentAbsence\Event\StudentAbsenceApprovalChangedEvent;
use App\StudentAbsence\Repository\StudentAbsenceRepositoryInterface;
use DateTime;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ApprovalHelper {
    public function __construct(private readonly StudentAbsenceRepositoryInterface $repository, private readonly EventDispatcherInterface $eventDispatcher)
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