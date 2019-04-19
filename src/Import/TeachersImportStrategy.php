<?php

namespace App\Import;

use App\Entity\Gender;
use App\Entity\Teacher;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\TeacherData;
use App\Utils\ArrayUtils;

class TeachersImportStrategy implements ImportStrategyInterface {

    private $teacherRepository;

    public function __construct(TeacherRepositoryInterface $teacherRepository) {
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @return array<string, Teacher>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->teacherRepository->findAll(),
            function(Teacher $teacher) {
                return $teacher->getExternalId();
            }
        );
    }

    /**
     * @param TeacherData $data
     * @return Teacher
     */
    public function createNewEntity($data) {
        $teacher = (new Teacher())
            ->setExternalId($data->getId());

        $this->updateEntity($teacher, $data);

        return $teacher;
    }

    /**
     * @param TeacherData $object
     * @param array<string, Teacher> $existingEntities
     * @return Teacher|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getAcronym()] ?? null;
    }

    /**
     * @param Teacher $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Teacher $entity
     * @param TeacherData $data
     */
    public function updateEntity($entity, $data): void {
        $entity->setAcronym($data->getAcronym());
        $entity->setTitle($data->getTitle());
        $entity->setGender(new Gender($data->getGender()));
        $entity->setFirstname($data->getFirstname());
        $entity->setLastname($data->getLastname());
    }

    /**
     * @param Teacher $entity
     */
    public function persist($entity): void {
        $this->teacherRepository
            ->persist($entity);
    }

    /**
     * @param Teacher $entity
     */
    public function remove($entity): void {
        $this->teacherRepository
            ->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->teacherRepository;
    }
}