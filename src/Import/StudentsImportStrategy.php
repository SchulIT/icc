<?php

namespace App\Import;

use App\Entity\Gender;
use App\Entity\Student;
use App\Repository\GradeRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudentData;
use App\Utils\ArrayUtils;

class StudentsImportStrategy implements ImportStrategyInterface {

    private $studentRepository;
    private $gradeRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, GradeRepositoryInterface $gradeRepository) {
        $this->studentRepository = $studentRepository;
        $this->gradeRepository = $gradeRepository;
    }

    /**
     * @return array<string,Student>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            function(Student $student) {
                return $student->getExternalId();
            }
        );
    }

    /**
     * @param StudentData $data
     * @return Student
     * @throws ImportException
     */
    public function createNewEntity($data) {
        $student = (new Student())
            ->setExternalId($data->getId());
        $this->updateEntity($student, $data);

        return $student;
    }

    /**
     * @param StudentData $object
     * @param array<string,Student> $existingEntities
     * @return Student|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Student $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Student $entity
     * @param StudentData $data
     * @throws ImportException
     */
    public function updateEntity($entity, $data): void {
        $entity->setFirstname($data->getFirstname());
        $entity->setLastname($data->getLastname());
        $entity->setGender(new Gender($data->getGender()));
        $entity->setStatus($data->getStatus());

        if($data->getGrade() !== null) {
            $grade = $this->gradeRepository->findOneByName($data->getGrade());

            if($grade === null) {
                throw new ImportException(sprintf('Grade "%s" does not exist (Student ID: "%s", Lastname: "%s")', $data->getGrade(), $data->getId(), $data->getLastname()));
            }

            $entity->setGrade($grade);
        } else {
            $entity->setGrade(null);
        }
    }

    /**
     * @inheritDoc
     */
    public function persist($entity): void {
        $this->studentRepository->persist($entity);
    }

    /**
     * @inheritDoc
     */
    public function remove($entity): void {
        $this->studentRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->studentRepository;
    }
}