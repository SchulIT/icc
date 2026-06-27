<?php

namespace App\Appointment\Recurring;

use App\Appointment\Entity\Appointment;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\UserTypeEntity;
use App\Common\Repository\SectionRepositoryInterface;
use App\Common\Repository\StudyGroupRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Framework\Import\Settings\ImportSettings;
use App\Framework\Utils\ArrayUtils;
use App\Framework\Utils\CollectionUtils;
use App\Common\Entity\Teacher;

readonly class RecurringAppointmentsManager {
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private SectionResolverInterface $sectionResolver,
        private SectionRepositoryInterface $sectionRepository,
        private StudyGroupRepositoryInterface $studyGroupRepository,
        private ImportSettings $settings
    ) { }

    public function persistRecurringAppointments(RecurringRequest $request): Result {
        $appointmentsToRecreate = $this->appointmentRepository->findRecurring($request->start, $request->end);
        $alreadyExistingAppointments = ArrayUtils::createArrayWithKeys(
            $this->appointmentRepository->findRecurring((clone $request->start)->modify('+1 year'), (clone $request->end)->modify('+1 year')),
            fn(Appointment $appointment): string => $appointment->getRecurringUuid()->toString()
        );

        $added = 0;
        $updated = 0;

        $this->appointmentRepository->beginTransaction();

        foreach($appointmentsToRecreate as $appointment) {
            $newAppointment = $alreadyExistingAppointments[$appointment->getRecurringUuid()->toString()] ?? null;

            if($newAppointment === null) {
                $newAppointment = (new Appointment())
                    ->setIsRecurring($appointment->isRecurring())
                    ->setRecurringUuid(clone $appointment->getRecurringUuid())
                    ->setRecurringGradeNames($appointment->getRecurringGradeNames());

                $newAppointment->setTitle($appointment->getTitle());
                $newAppointment->setContent($appointment->getContent());
                $newAppointment->setStart((clone $appointment->getStart())->modify('+1 year'));
                $newAppointment->setEnd((clone $appointment->getEnd())->modify('+1 year'));
                $newAppointment->setLocation($appointment->getLocation());
                $newAppointment->setAllDay($appointment->isAllDay());
                $newAppointment->setExternalOrganizers($appointment->getExternalOrganizers());
                $newAppointment->setCategory($appointment->getCategory());
                $newAppointment->setIsConfirmed($appointment->isConfirmed());

                CollectionUtils::synchronize(
                    $newAppointment->getOrganizers(),
                    $appointment->getOrganizers()->toArray(),
                    fn(Teacher $teacher): int => $teacher->getId()
                );

                CollectionUtils::synchronize(
                    $newAppointment->getVisibilities(),
                    $appointment->getVisibilities()->toArray(),
                    fn(UserTypeEntity $entity): int => $entity->getId()
                );

                $added++;
            } else {
                $updated++;
            }

            // Update study groups
            if($newAppointment->getRecurringGradeNames() !== null) {
                $studyGroups = [ ];

                $section = $this->sectionResolver->getSectionForDate($newAppointment->getStart());

                if($section !== null) {
                    $studyGroups = $this->studyGroupRepository->findAllByExternalId($newAppointment->getRecurringUuid(), $section);

                    if(count($studyGroups) === 0 && $this->settings->getFallbackSection() !== null && ($fallbackSection = $this->sectionRepository->findOneById($this->settings->getFallbackSection())) !== null) {
                        $studyGroups = $this->studyGroupRepository->findAllByExternalId($newAppointment->getRecurringUuid(), $fallbackSection);
                    }
                }

                CollectionUtils::synchronize(
                    $newAppointment->getStudyGroups(),
                    $studyGroups,
                    fn(StudyGroup $studyGroup): int => $studyGroup->getId()
                );
            } else if($newAppointment->getStudyGroups()->count() > 0) {
                $newAppointment->getStudyGroups()->clear();
            }

            $this->appointmentRepository->persist($newAppointment);
        }

        $this->appointmentRepository->commit();
        return new Result($added, $updated);
    }
}
