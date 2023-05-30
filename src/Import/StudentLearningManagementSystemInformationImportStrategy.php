<?php

namespace App\Import;

use App\Entity\LearningManagementSystem;
use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;
use App\Repository\LearningManagementSystemRepositoryInterface;
use App\Repository\StudentLearningManagementInformationRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudentLearningManagementSystemData;
use App\Request\Data\StudentLearningManagementSystemsData;
use App\Utils\ArrayUtils;

class StudentLearningManagementSystemInformationImportStrategy implements ReplaceImportStrategyInterface, InitializeStrategyInterface {

    private array $students = [ ];
    private array $lms = [ ];

    public function __construct(private readonly StudentLearningManagementInformationRepositoryInterface $repository, private readonly StudentRepositoryInterface $studentRepository, private readonly LearningManagementSystemRepositoryInterface $lmsRepository) { }

    public function initialize($requestData): void {
        $this->students = ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            fn(Student $student) => $student->getExternalId()
        );

        $this->lms = ArrayUtils::createArrayWithKeys(
            $this->lmsRepository->findAll(),
            fn(LearningManagementSystem $lms) => $lms->getExternalId()
        );
    }

    public function getEntityClassName(): string {
        return StudentLearningManagementSystemInformation::class;
    }

    /**
     * @param StudentLearningManagementSystemsData $data
     * @return StudentLearningManagementSystemData[]
     */
    public function getData($data): array {
        return $data->getConsents();
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }

    public function removeAll($data): void {
        $this->repository->removeAll();
    }

    /**
     * @param StudentLearningManagementSystemData $data
     * @param StudentLearningManagementSystemsData $requestData
     * @return void
     * @throws EntityIgnoredException
     */
    public function persist($data, $requestData): void {
        $student = $this->students[$data->getStudent()] ?? null;

        if($student === null) {
            throw new EntityIgnoredException($data, sprintf('Kind mit der ID "%s" nicht gefunden.', $data->getStudent()));
        }

        $lms = $this->lms[$data->getLms()] ?? null;

        if($lms === null) {
            throw new EntityIgnoredException($data, sprintf('Lernplattform mit der ID "%s" nicht gefunden.', $data->getLms()));
        }

        $info = new StudentLearningManagementSystemInformation();
        $info->setStudent($student);
        $info->setLms($lms);
        $info->setIsConsented($data->isConsented());
        $info->setIsConsentObtained($data->isConsentObtained());
        $info->setIsAudioConsented($data->isAudioConsented());
        $info->setIsVideoConsented($data->isVideoConsented());
        $info->setUsername($data->getUsername());
        $info->setPassword($data->getPassword());

        $this->repository->persist($info);
    }
}