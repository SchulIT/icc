<?php

namespace App\Import;

use App\Entity\Appointment;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\UserTypeEntity;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\UserTypeEntityRepositoryInterface;
use App\Request\Data\AppointmentData;
use App\Request\Data\AppointmentsData;
use App\Section\SectionResolverInterface;
use App\Settings\AppointmentsSettings;
use App\Settings\ImportSettings;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;

class AppointmentsImportStrategy implements ImportStrategyInterface {

    private bool $isInitialized = false;

    /**
     * @var UserTypeEntity[]
     */
    private array $visibilities = [];

    public function __construct(private AppointmentRepositoryInterface $appointmentRepository, private AppointmentCategoryRepositoryInterface $appointmentCategoryRepository, private UserTypeEntityRepositoryInterface $userTypeEntityRepository, private StudyGroupRepositoryInterface $studentGroupRepository, private TeacherRepositoryInterface $teacherRepository, private SectionResolverInterface $sectionResolver, private SectionRepositoryInterface $sectionRepository, private ImportSettings $settings)
    {
    }

    private function initializeIfNecessary() {
        if($this->isInitialized === true) {
            return;
        }

        $this->visibilities = $this->userTypeEntityRepository->findAll();
        $this->isInitialized = true;
    }

    /**
     * @param AppointmentsData $requestData
     * @return array<string, Appointment>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            array_filter(
                $this->appointmentRepository->findAll(),
                fn(Appointment $appointment) => !empty($appointment->getExternalId())),
            fn(Appointment $appointment) => $appointment->getExternalId()
        );
    }

    /**
     * @param AppointmentData $data
     * @param AppointmentsData $requestData
     * @return Appointment
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $appointment = (new Appointment())
            ->setExternalId($data->getId());
        $this->updateEntity($appointment, $data, $requestData);

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
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Appointment $entity
     * @param AppointmentData $data
     * @param AppointmentsData $requestData
     * @throws ImportException
     * @throws SectionNotResolvableException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $this->initializeIfNecessary();

        $entity->setStart($data->getStart());
        $entity->setEnd($data->getEnd());
        $entity->setContent($data->getContent());
        $entity->setAllDay($data->isAllDay());
        $entity->setLocation($data->getLocation());
        $entity->setTitle($data->getSubject());
        $entity->setMarkStudentsAbsent($data->isMarkStudentsAbsent());

        $category = $this->appointmentCategoryRepository->findOneByExternalId($data->getCategory());

        if($category === null) {
            throw new ImportException(sprintf('Category "%s" on appointment "%s" (ID: %s) was not found.', $data->getCategory(), $data->getSubject(), $data->getId()));
        }

        $entity->setCategory($category);

        $section = $this->sectionResolver->getSectionForDate($entity->getStart());

        if($section === null) {
            throw new SectionNotResolvableException($entity->getStart());
        }

        $studyGroups = $this->studentGroupRepository->findAllByExternalId($data->getStudyGroups(), $section);
        if(count($data->getStudyGroups()) > 0 && count($studyGroups) === 0
            && $this->settings->getFallbackSection() !== null && ($fallbackSection = $this->sectionRepository->findOneById($this->settings->getFallbackSection())) !== null) {
            $studyGroups = $this->studentGroupRepository->findAllByExternalId($data->getStudyGroups(), $fallbackSection);
        }

        CollectionUtils::synchronize(
            $entity->getStudyGroups(),
            $studyGroups,
            fn(StudyGroup $group) => $group->getId()
        );

        CollectionUtils::synchronize(
            $entity->getOrganizers(),
            $this->teacherRepository->findAllByAcronym($data->getOrganizers()),
            fn(Teacher $teacher) => $teacher->getId()
        );

        CollectionUtils::synchronize(
            $entity->getVisibilities(),
            array_filter($this->visibilities, fn(UserTypeEntity $visibility) => in_array($visibility->getUserType()->getValue(), $data->getVisibilities())),
            fn(UserTypeEntity $visibility) => $visibility->getId()
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
    public function remove($entity, $requestData): bool {
        $this->appointmentRepository->remove($entity);
        return true;
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

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Appointment::class;
    }
}