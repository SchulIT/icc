<?php

namespace App\Import\External\WestermannZsv;

use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;
use App\Import\External\CreateOrUpdateLmsStudentInfoMessage;
use App\Repository\StudentLearningManagementInformationRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Utils\ArrayUtils;
use League\Csv\Reader;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class WestermannZvsImporter {
    public function __construct(private StudentRepositoryInterface $studentRepository,
                                private MessageBusInterface $messageBus) {

    }

    public function importAsync(ImportRequest $request): void {
        $reader = Reader::createFromString($request->csv->getContent());
        $reader->setHeaderOffset(0);
        $reader->setDelimiter($request->delimiter);
        $reader->setEscape('');

        $students = ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            fn(Student $student) => $student->getEmail()
        );

        foreach($reader->getRecords() as $record) {
            $username = $record['Benutzername'];
            $password = $record['Kennwort'];

            /** @var Student|null $student */
            $student = $students[$username] ?? null;

            if($student === null) {
                continue;
            }

            $this->messageBus->dispatch(
                new CreateOrUpdateLmsStudentInfoMessage($student->getId(), $request->lms->getId(), $username, $password)
            );
        }
    }
}