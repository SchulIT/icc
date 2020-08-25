<?php

namespace App\Import;

use App\Entity\StudyGroupMembership;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupMembershipRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudyGroupMembershipData;
use App\Request\Data\StudyGroupMembershipsData;

class StudyGroupMembershipImportStrategy implements ReplaceImportStrategyInterface {

    private $studyGroupMembershipRepository;
    private $studyGroupRepository;
    private $studentRepository;

    public function __construct(StudyGroupMembershipRepositoryInterface $studyGroupMembershipRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository,
                                StudentRepositoryInterface $studentRepository) {
        $this->studyGroupMembershipRepository = $studyGroupMembershipRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->studentRepository = $studentRepository;
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->studyGroupMembershipRepository;
    }

    public function removeAll(): void {
        $this->studyGroupMembershipRepository->removeAll();
    }

    /**
     * @param StudyGroupMembershipData $data
     * @throws EntityIgnoredException
     */
    public function persist($data): void {
        $studyGroup = $this->studyGroupRepository->findOneByExternalId($data->getStudyGroup());

        if($studyGroup === null) {
            throw new EntityIgnoredException($data, sprintf('Study group with ID "%s" was not found.', $data->getStudyGroup()));
        }

        $student = $this->studentRepository->findOneByExternalId($data->getStudent());

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