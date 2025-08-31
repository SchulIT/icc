<?php

namespace App\Import\External;

use App\Entity\StudentLearningManagementSystemInformation;
use App\Repository\LearningManagementSystemRepositoryInterface;
use App\Repository\StudentLearningManagementInformationRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateOrUpdateLmsStudentInfoMessageHandler {

    public function __construct(private StudentLearningManagementInformationRepositoryInterface $repository,
                                private LearningManagementSystemRepositoryInterface $lmsRepository,
                                private StudentRepositoryInterface $studentRepository,
                                private LoggerInterface $logger) {

    }

    public function __invoke(CreateOrUpdateLmsStudentInfoMessage $message): void {
        $student = $this->studentRepository->findOneById($message->studentId);
        $lms = $this->lmsRepository->findOneById($message->lmsId);

        if($student === null) {
            $this->logger->warning(sprintf('Schüler mit der ID "%s" nicht gefunden. LMS-Credentials werden nicht aktualisiert.', $message->studentId));
            return;
        }

        if($lms === null) {
            $this->logger->warning(sprintf('LMS mit der ID "%s" nicht gefunden. LMS-Credentials werden nicht aktualisiert.', $message->lmsId));
            return;
        }

        $info = $this->repository->findOneByStudentAndLms($student, $lms);

        if($info === null) {
            $info = new StudentLearningManagementSystemInformation();
            $info->setStudent($student);
            $info->setLms($lms);
        }

        $info->setUsername($message->username);
        $info->setPassword($message->password);
        $info->setIsConsented(true);
        $info->setIsConsentObtained(true);

        $this->repository->persist($info);
    }
}