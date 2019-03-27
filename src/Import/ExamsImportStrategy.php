<?php

namespace App\Import;

use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\ExamRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\ExamData;
use App\Utils\ArrayCollectionUtils;

class ExamsImportStrategy implements ImportStrategyInterface {

    private $examRepository;
    private $tuitionRepository;
    private $studentRepository;
    private $collectionUtils;

    public function __construct(ExamRepositoryInterface $examRepository, TuitionRepositoryInterface $tuitionRepository,
                                StudentRepositoryInterface $studentRepository, ArrayCollectionUtils $collectionUtils) {
        $this->examRepository = $examRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->studentRepository = $studentRepository;
        $this->collectionUtils = $collectionUtils;
    }

    /**
     * @return Exam[]
     */
    public function getExistingEntities(): array {
        $exams = [ ];

        foreach($this->examRepository->findAll() as $exam) {
            $exams[$exam->getExternalId()] = $exam;
        }

        return $exams;
    }

    /**
     * @param ExamData $data
     * @return Exam
     */
    public function createNewEntity($data) {
        $exam = (new Exam())
            ->setExternalId($data->getId());
        $this->updateEntity($exam, $data);

        return $exam;
    }

    /**
     * @param Exam $object
     * @param Exam[] $existingEntities
     * @return Exam|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getExternalId()] ?? null;
    }

    /**
     * @param Exam $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Exam $entity
     * @param ExamData $data
     */
    public function updateEntity($entity, $data): void {
        $entity->setDate($data->getDate());
        $entity->setDescription($data->getDescription());
        $entity->setLessonStart($data->getLessonStart());
        $entity->setLessonEnd($data->getLessonEnd());
        $entity->setRooms($data->getRooms());

        $this->collectionUtils->synchronize(
            $entity->getStudents(),
            $this->studentRepository->findAllByExternalId($data->getStudents()),
            function(Student $student) {
                return $student->getId();
            }
        );

        $this->collectionUtils->synchronize(
            $entity->getTuitions(),
            $this->tuitionRepository->findAllByExternalId($data->getTuitions()),
            function(Tuition $tuition) {
                return $tuition->getId();
            }
        );
    }

    /**
     * @param Exam $entity
     */
    public function persist($entity): void {
        $this->examRepository->persist($entity);
    }

    /**
     * @param Exam $entity
     */
    public function remove($entity): void {
        $this->examRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->examRepository;
    }
}