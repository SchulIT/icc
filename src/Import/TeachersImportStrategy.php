<?php

namespace App\Import;

use App\Entity\Gender;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TeacherTag;
use App\Repository\SectionRepositoryInterface;
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
    private $sectionRepository;

    public function __construct(TeacherRepositoryInterface $teacherRepository, SubjectRepositoryInterface $subjectRepository,
                                TeacherTagRepositoryInterface $teacherTagRepository, SectionRepositoryInterface $sectionRepository) {
        $this->teacherRepository = $teacherRepository;
        $this->subjectRepository = $subjectRepository;
        $this->teacherTagRepository = $teacherTagRepository;
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * @param TeachersData $requestData
     * @return array<string, Teacher>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->teacherRepository->findAll(),
            function(Teacher $teacher) {
                return $teacher->getExternalId();
            }
        );
    }

    /**
     * @param TeacherData $data
     * @param TeachersData $requestData
     * @return Teacher
     */
    public function createNewEntity($data, $requestData) {
        $teacher = (new Teacher())
            ->setExternalId($data->getId());

        $this->updateEntity($teacher, $data, $requestData);

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
     * @param TeachersData $requestData
     * @throws SectionNotFoundException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setAcronym($data->getAcronym());
        $entity->setTitle($data->getTitle());
        $entity->setGender(new Gender($data->getGender()));
        $entity->setFirstname($data->getFirstname());
        $entity->setLastname($data->getLastname());
        $entity->setEmail($data->getEmail());

        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        if($entity->getSections()->contains($section) === false) {
            $entity->addSection($section);
        }

        CollectionUtils::synchronize(
            $entity->getSubjects(),
            $this->subjectRepository->findAllByExternalId($data->getSubjects()),
            function(Subject $subject) {
                return $subject->getId();
            }
        );

        CollectionUtils::add($entity->getTags(), $this->createTagCollection($data->getTags()), function(TeacherTag $tag) {
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
    public function remove($entity, $requestData): bool {
        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section !== null && $entity->getSections()->contains($section)) {
            $entity->removeSection($section);
            $this->teacherRepository->persist($entity);

            if($entity->getSections()->count() === 0) {
                $this->teacherRepository->remove($entity);
                return true;
            }
        }

        return false;
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

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Teacher::class;
    }
}