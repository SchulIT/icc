<?php

namespace App\Import;

use App\Entity\Appointment;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\AppointmentData;
use App\Utils\CollectionUtils;
use App\Utils\ArrayUtils;

class AppointmentsImportStrategy implements ImportStrategyInterface {

    private $appointmentRepository;
    private $appointmentCategoryRepository;
    private $studentGroupRepository;
    private $teacherRepository;

    public function __construct(AppointmentRepositoryInterface $appointmentRepository, AppointmentCategoryRepositoryInterface $appointmentCategoryRepository,
                                StudyGroupRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository) {
        $this->appointmentRepository = $appointmentRepository;
        $this->appointmentCategoryRepository = $appointmentCategoryRepository;
        $this->studentGroupRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @return array<string, Appointment>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->appointmentRepository->findAll(),
            function (Appointment $appointment) {
                return $appointment->getExternalId();
            }
        );
    }

    /**
     * @param AppointmentData $data
     * @return Appointment
     * @throws ImportException
     */
    public function createNewEntity($data) {
        $appointment = (new Appointment())
            ->setExternalId($data->getId());
        $this->updateEntity($appointment, $data);

        return $appointment;
    }

    /**
     * @param AppointmentData $object
     * @param array<string, Appointment> $existingEntities
     * @return Appointment|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Appointment $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Appointment $entity
     * @param AppointmentData $data
     * @throws ImportException
     */
    public function updateEntity($entity, $data): void {
        $entity->setStart($data->getStart());
        $entity->setEnd($data->getEnd());
        $entity->setContent($data->getContent());
        $entity->setAllDay($data->isAllDay());
        $entity->setIsHiddenFromStudents($data->isHiddenFromStudents());
        $entity->setLocation($data->getLocation());
        $entity->setSubject($data->getSubject());

        $category = $this->appointmentCategoryRepository->findOneByExternalId($data->getCategory());

        if($category === null) {
            throw new ImportException(sprintf('Category "%s" on appointment "%s" (ID: %s) was not found.', $data->getCategory(), $data->getSubject(), $data->getId()));
        }

        $entity->setCategory($category);

        CollectionUtils::synchronize(
            $entity->getStudyGroups(),
            $this->studentGroupRepository->findAllByExternalId($data->getStudyGroups()),
            function (StudyGroup $group) {
                return $group->getId();
            }
        );

        CollectionUtils::synchronize(
            $entity->getOrganizers(),
            $this->teacherRepository->findAllByAcronym($data->getOrganizers()),
            function(Teacher $teacher) {
                return $teacher->getId();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function persist($entity): void {
        $this->appointmentRepository->persist($entity);
    }

    /**
     * @inheritDoc
     */
    public function remove($entity): void {
        $this->appointmentRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->appointmentRepository;
    }
}