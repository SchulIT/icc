<?php

namespace App\Import;

use App\Entity\Appointment;
use App\Entity\AppointmentVisibility;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\AppointmentVisibilityRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\AppointmentData;
use App\Request\Data\AppointmentsData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;

class AppointmentsImportStrategy implements ImportStrategyInterface {

    private $appointmentRepository;
    private $appointmentCategoryRepository;
    private $appointmentVisibilityRepository;
    private $studentGroupRepository;
    private $teacherRepository;

    private $isInitialized = false;

    /**
     * @var AppointmentVisibility[]
     */
    private $visibilities = [];

    public function __construct(AppointmentRepositoryInterface $appointmentRepository, AppointmentCategoryRepositoryInterface $appointmentCategoryRepository,
                                AppointmentVisibilityRepositoryInterface $appointmentVisibilityRepository, StudyGroupRepositoryInterface $studentRepository,
                                TeacherRepositoryInterface $teacherRepository) {
        $this->appointmentRepository = $appointmentRepository;
        $this->appointmentCategoryRepository = $appointmentCategoryRepository;
        $this->appointmentVisibilityRepository = $appointmentVisibilityRepository;
        $this->studentGroupRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
    }

    private function initializeIfNecessary() {
        if($this->isInitialized === true) {
            return;
        }

        $this->visibilities = $this->appointmentVisibilityRepository->findAll();
        $this->isInitialized = true;
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
        $this->initializeIfNecessary();

        $entity->setStart($data->getStart());
        $entity->setEnd($data->getEnd());
        $entity->setContent($data->getContent());
        $entity->setAllDay($data->isAllDay());
        $entity->setLocation($data->getLocation());
        $entity->setTitle($data->getSubject());

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

        CollectionUtils::synchronize(
            $entity->getVisibilities(),
            array_filter($this->visibilities, function(AppointmentVisibility $visibility) use ($data) {
                return in_array($visibility->getUserType()->getValue(), $data->getVisibilities());
            }),
            function(AppointmentVisibility $visibility) {
                return $visibility->getId();
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

    /**
     * @param AppointmentsData $data
     * @return AppointmentData[]
     */
    public function getData($data): array {
        return $data->getAppointments();
    }
}