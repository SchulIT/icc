<?php

namespace App\Import;

use App\Entity\Gender;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TeacherTag;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TeacherTagRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\TeacherData;
use App\Request\Data\TeachersData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;
use Doctrine\Common\Collections\ArrayCollection;

class TeachersImportStrategy implements ImportStrategyInterface {

    private $teacherRepository;
    private $subjectRepository;
    private $teacherTagRepository;

    public function __construct(TeacherRepositoryInterface $teacherRepository, SubjectRepositoryInterface $subjectRepository, TeacherTagRepositoryInterface $teacherTagRepository) {
        $this->teacherRepository = $teacherRepository;
        $this->subjectRepository = $subjectRepository;
        $this->teacherTagRepository = $teacherTagRepository;
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
        return $existingEntities[$object->getId()] ?? null;
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
        $entity->setEmail($data->getEmail());

        CollectionUtils::synchronize(
            $entity->getSubjects(),
            $this->subjectRepository->findAllByExternalId($data->getSubjects()),
            function(Subject $subject) {
                return $subject->getId();
            }
        );

        CollectionUtils::synchronize($entity->getTags(), $this->createTagCollection($data->getTags()), function(TeacherTag $tag) {
            return $tag->getExternalId();
        });
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

    /**
     * @param string[] $tagExternalIds
     * @return TeacherTag[]
     */
    private function createTagCollection(array $tagExternalIds): array {
        $tags = new ArrayCollection($this->teacherTagRepository->findAll());

        return $tags->filter(function(TeacherTag $tag) use($tagExternalIds) {
            return in_array($tag->getExternalId(), $tagExternalIds);
        })->toArray();
    }

    /**
     * @param TeachersData $data
     * @return TeacherData[]
     */
    public function getData($data): array {
        return $data->getTeachers();
    }
}