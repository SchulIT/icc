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

    private $studyGroupMembershipRepository;
    private $studyGroupRepository;
    private $studentRepository;
    private $sectionRepository;

    private $studyGroups = [ ];
    private $students = [ ];

    public function __construct(StudyGroupMembershipRepositoryInterface $studyGroupMembershipRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository,
                                StudentRepositoryInterface $studentRepository, SectionRepositoryInterface $sectionRepository) {
        $this->studyGroupMembershipRepository = $studyGroupMembershipRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->studentRepository = $studentRepository;
        $this->sectionRepository = $sectionRepository;
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
            function(StudyGroup $studyGroup) {
                return $studyGroup->getExternalId();
            }
        );

        $this->students = ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAllBySection($section),
            function(Student $student) {
                return $student->getExternalId();
            }
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
            throw new EntityIgnoredException($data, sprintf('Study group with ID "%s" was not found.', $data->getStudyGroup()));
        }

        $student = $this->students[$data->getStudent()] ?? null;

        if($student === null) {
            throw new EntityIgnoredException($data, sprintf('Student with ID "%s" was not found.', $data->getStudent()));
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