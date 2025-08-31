<?php

namespace App\Import\External\Bildungslogin;

use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;
use App\Repository\StudentLearningManagementInformationRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Utils\ArrayUtils;
use League\Csv\Reader;

readonly class BildungsloginImporter {

    public function __construct(private StudentLearningManagementInformationRepositoryInterface $repository,
                                private StudentRepositoryInterface $studentRepository) {

    }


    public function import(ImportRequest $request): void {
        $summaryReader = Reader::createFromString($request->summaryCsv->getContent());
        $this->configureReader($summaryReader);

        $passwordReader = Reader::createFromString($request->passwordsCsv->getContent());
        $this->configureReader($passwordReader);

        $map = [ ];

        foreach($summaryReader->getRecords() as $record) {
            $username = $record['username'];
            $externalId = $record['record_uid'];

            $map[$username] = [
                'id' => $externalId,
            ];
        }

        foreach($passwordReader->getRecords() as $record) {
            $username = $record['username'];
            $password = $record['password'];

            if(isset($map[$username])) {
                $map[$username]['password'] = $password;
            }
        }

        $existingData = ArrayUtils::createArrayWithKeys(
            $this->repository->findByLms($request->lms),
            fn(StudentLearningManagementSystemInformation $information) => $information->getStudent()->getExternalId()
        );

        $students = ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            fn(Student $student) => $student->getExternalId()
        );

        $this->repository->beginTransaction();

        foreach($map as $username => $infoArray) {
            $studentId = $infoArray['id'];
            $password = $infoArray['password'];

            if(isset($existingData[$username])) {
                $info = $existingData[$username];
            } else {
                $student = $students[$studentId] ?? null;

                if($student === null) {
                    continue;
                }

                $info = (new StudentLearningManagementSystemInformation())
                    ->setStudent($student)
                    ->setLms($request->lms);
            }

            $info->setUsername($username);
            $info->setPassword($password);
            $info->setIsConsented(true);
            $info->setIsConsentObtained(true);

            $this->repository->persist($info);
        }

        $this->repository->commit();
    }

    private function configureReader(Reader $reader): void {
        $reader->setHeaderOffset(0);
        $reader->setDelimiter(',');
        $reader->setEscape('');
    }
}