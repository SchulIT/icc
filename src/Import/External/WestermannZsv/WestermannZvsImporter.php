<?php

namespace App\Import\External\WestermannZsv;

use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;
use App\Repository\StudentLearningManagementInformationRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Utils\ArrayUtils;
use League\Csv\Reader;

readonly class WestermannZvsImporter {
    public function __construct(private StudentLearningManagementInformationRepositoryInterface $repository,
                                private StudentRepositoryInterface $studentRepository) {

    }

    public function import(ImportRequest $request): void {
        $reader = Reader::createFromString($request->csv->getContent());
        $reader->setHeaderOffset(0);
        $reader->setDelimiter($request->delimiter);
        $reader->setEscape('');

        $students = ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            fn(Student $student) => $student->getEmail()
        );

        $existingData = ArrayUtils::createArrayWithKeys(
            $this->repository->findByLms($request->lms),
            fn(StudentLearningManagementSystemInformation $information) => $information->getStudent()->getEmail()
        );

        $this->repository->beginTransaction();

        foreach($reader->getRecords() as $record) {
            $username = $record['Benutzername'];
            $password = $record['Kennwort'];

            if(isset($existingData[$username])) {
                $info = $existingData[$username];
            } else {
                $student = $students[$username] ?? null;

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
}