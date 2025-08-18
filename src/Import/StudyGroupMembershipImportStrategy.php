<?php

namespace App\Import;

use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupMembershipRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudyGroupMembershipData;
use App\Request\Data\StudyGroupMembershipsData;
use App\Utils\ArrayUtils;

class StudyGroupMembershipImportStrategy implements ReplaceImportStrategyInterface, InitializeStrategyInterface {

    private array $studyGroups = [ ];
    private array $students = [ ];

    public function __construct(private StudyGroupMembershipRepositoryInterface $studyGroupMembershipRepository, private StudyGroupRepositoryInterface $studyGroupRepository, private StudentRepositoryInterface $studentRepository, private SectionRepositoryInterface $sectionRepository)
    {
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->studyGroupMembershipRepository;
    }

    /**
     * @param StudyGroupMembershipsData $requestData
     * @throws SectionNotFoundException
     */
    public function initialize($requestData): void {
        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        $this->studyGroups = ArrayUtils::createArrayWithKeys(
            $this->studyGroupRepository->findAllBySection($section),
            fn(StudyGroup $studyGroup) => $studyGroup->getExternalId()
        );

        $this->students = ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAllBySection($section),
            fn(Student $student) => $student->getExternalId()
        );
    }

    /**
     * @param StudyGroupMembershipsData $requestData
     * @throws SectionNotFoundException
     */
    public function removeAll($requestData): void {
        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        $this->studyGroupMembershipRepository->removeAll($section);
    }

    /**
     * @param StudyGroupMembershipData $data
     * @throws EntityIgnoredException
     */
    public function persist($data, $requestData): void {
        $studyGroup = $this->studyGroups[$data->getStudyGroup()] ?? null;

        if($studyGroup === null) {
            throw new EntityIgnoredException($data, sprintf('Lerngruppe mit ID "%s" wurde nicht gefunden.', $data->getStudyGroup()));
        }

        $student = $this->students[$data->getStudent()] ?? null;

        if($student === null) {
            throw new EntityIgnoredException($data, sprintf('Kind mit ID "%s" wurde nicht gefunden (Lerngruppe %s).', $data->getStudent(), $data->getStudyGroup()));
        }

        $membership = (new StudyGroupMembership())
            ->setStudyGroup($studyGroup)
            ->setStudent($student)
            ->setType($data->getType());

        $this->studyGroupMembershipRepository->persist($membership);
    }

    /**
     * @param StudyGroupMembershipsData $data
     * @return StudyGroupMembershipData[]
     */
    public function getData($data): array {
        return $data->getMemberships();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return StudyGroupMembership::class;
    }
}