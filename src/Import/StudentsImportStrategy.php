<?php

namespace App\Import;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\PrivacyCategory;
use App\Entity\Student;
use App\Repository\GradeRepositoryInterface;
use App\Repository\PrivacyCategoryRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudentData;
use App\Request\Data\StudentsData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;

class StudentsImportStrategy implements ImportStrategyInterface, InitializeStrategyInterface {

    private array $privacyCategoriesCache = [ ];

    public function __construct(private StudentRepositoryInterface $studentRepository, private PrivacyCategoryRepositoryInterface $privacyCategoryRepository, private SectionRepositoryInterface $sectionRepository)
    {
    }

    public function initialize($requestData): void {
        $this->privacyCategoriesCache = ArrayUtils::createArrayWithKeys(
            $this->privacyCategoryRepository->findAll(),
            fn(PrivacyCategory $category) => $category->getExternalId()
        );
    }

    /**
     * @param StudentsData $requestData
     * @return array<string,Student>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            fn(Student $student) => $student->getExternalId()
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
        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        $entity->setFirstname($data->getFirstname());
        $entity->setLastname($data->getLastname());
        $entity->setGender(Gender::from($data->getGender()));
        $entity->setBirthday($data->getBirthday());
        $entity->setStatus($data->getStatus());
        $entity->setEmail($data->getEmail());
        $entity->setUniqueIdentifier(sprintf('%s_%s_%s', $entity->getLastname(), $entity->getFirstname(), $entity->getBirthday()->format('Ymd')));

        if($entity->getSections()->contains($section) === false) {
            $entity->addSection($section);
        }

        $approvedPrivacyCategories = ArrayUtils::findAllWithKeys(
            $this->privacyCategoriesCache,
            $data->getApprovedPrivacyCategories()
        );

        CollectionUtils::synchronize(
            $entity->getApprovedPrivacyCategories(),
            $approvedPrivacyCategories,
            fn(PrivacyCategory $category) => $category->getId()
        );
    }

    /**
     * @inheritDoc
     */
    public function persist($entity): void {
        $this->studentRepository->persist($entity);
    }

    /**
     * @param Student $entity
     * @param StudentsData $requestData
     */
    public function remove($entity, $requestData): bool {
        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section !== null && $entity->getSections()->contains($section)) {
            $entity->removeSection($section);
            $this->studentRepository->persist($entity);

            if($entity->getSections()->count() === 0) {
                $this->studentRepository->remove($entity);
                return true;
            }
        }

        return false;
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

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Student::class;
    }
}