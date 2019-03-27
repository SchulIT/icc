<?php

namespace App\Import;

use App\Entity\Gender;
use App\Entity\Student;
use App\Entity\StudentStatus;
use App\Repository\GradeRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudentData;

class StudentsImportStrategy implements ImportStrategyInterface {

    private $studentRepository;
    private $gradeRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, GradeRepositoryInterface $gradeRepository) {
        $this->studentRepository = $studentRepository;
        $this->gradeRepository = $gradeRepository;
    }

    /**
     * @inheritDoc
     */
    public function getExistingEntities(): array {
        /** @var array<string,Student> $currentStudents */
        $currentStudents = [ ];

        foreach($this->studentRepository->findAll() as $student) {
            $currentStudents[$student->getExternalId()] = $student;
        }

        return $currentStudents;
    }

    /**
     * @param StudentData $data
     * @return Student
     */
    public function createNewEntity($data) {
        $student = (new Student())
            ->setExternalId($data->getId());
        $this->updateEntity($student, $data);

        return $student;
    }

    /**
     * @param StudentData $object
     * @param Student[] $existingEntities
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
     * @return void
     */
    public function updateEntity($entity, $data): void {
        $entity->setFirstname($data->getFirstname());
        $entity->setLastname($data->getLastname());
        $entity->setGender(new Gender($data->getGender()));
        $entity->setStatus(new StudentStatus($data->getStatus()));

        if($data->getGrade() !== null) {
            $entity->setGrade($this->gradeRepository->findOneByName($data->getGrade()));
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