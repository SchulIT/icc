<?php

namespace App\Import;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\PrivacyCategory;
use App\Entity\Student;
use App\Repository\GradeRepositoryInterface;
use App\Repository\PrivacyCategoryRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudentData;
use App\Request\Data\StudentsData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;

class StudentsImportStrategy implements ImportStrategyInterface, InitializeStrategyInterface {

    private $studentRepository;
    private $gradeRepository;
    private $privacyCategoryRepository;

    private $gradesCache = [ ];
    private $privacyCategoriesCache = [ ];

    public function __construct(StudentRepositoryInterface $studentRepository, GradeRepositoryInterface $gradeRepository, PrivacyCategoryRepositoryInterface $privacyCategoryRepository) {
        $this->studentRepository = $studentRepository;
        $this->gradeRepository = $gradeRepository;
        $this->privacyCategoryRepository = $privacyCategoryRepository;
    }

    public function initialize(): void {
        $this->gradesCache = ArrayUtils::createArrayWithKeys(
            $this->gradeRepository->findAll(),
            function(Grade $grade) {
                return $grade->getExternalId();
            }
        );

        $this->privacyCategoriesCache = ArrayUtils::createArrayWithKeys(
            $this->privacyCategoryRepository->findAll(),
            function(PrivacyCategory $category) {
                return $category->getExternalId();
            }
        );
    }

    /**
     * @param StudentsData $requestData
     * @return array<string,Student>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            function(Student $student) {
                return $student->getExternalId();
            }
        );
    }

    /**
     * @param StudentData $data
     * @param StudentsData $requestData
     * @return Student
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $student = (new Student())
            ->setExternalId($data->getId());
        $this->updateEntity($student, $data, $requestData);

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
     * @param StudentsData $requestData
     * @throws ImportException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setFirstname($data->getFirstname());
        $entity->setLastname($data->getLastname());
        $entity->setGender(new Gender($data->getGender()));
        $entity->setStatus($data->getStatus());
        $entity->setEmail($data->getEmail());

        if($data->getGrade() !== null) {
            $grade = $this->gradesCache[$data->getGrade()] ?? null;

            if($grade === null) {
                throw new ImportException(sprintf('Grade "%s" does not exist (Student ID: "%s", Lastname: "%s")', $data->getGrade(), $data->getId(), $data->getLastname()));
            }

            $entity->setGrade($grade);
        } else {
            $entity->setGrade(null);
        }

        $approvedPrivacyCategories = ArrayUtils::findAllWithKeys(
            $this->privacyCategoriesCache,
            $data->getApprovedPrivacyCategories()
        );

        CollectionUtils::synchronize(
            $entity->getApprovedPrivacyCategories(),
            $approvedPrivacyCategories,
            function(PrivacyCategory $category) {
                return $category->getId();
            }
        );
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

    /**
     * @param StudentsData $data
     * @return StudentData[]
     */
    public function getData($data): array {
        return $data->getStudents();
    }
}