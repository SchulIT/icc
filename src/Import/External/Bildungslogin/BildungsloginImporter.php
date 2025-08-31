<?php

namespace App\Import\External\Bildungslogin;

use App\Entity\Student;
use App\Import\External\CreateOrUpdateLmsStudentInfoMessage;
use App\Repository\StudentRepositoryInterface;
use App\Utils\ArrayUtils;
use League\Csv\Reader;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class BildungsloginImporter {

    public function __construct(private StudentRepositoryInterface $studentRepository, private MessageBusInterface $messageBus) {

    }


    public function importAsync(ImportRequest $request): void {
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

        $students = ArrayUtils::createArrayWithKeys(
            $this->studentRepository->findAll(),
            fn(Student $student) => $student->getExternalId()
        );

        foreach($map as $username => $infoArray) {
            $studentId = $infoArray['id'];
            $password = $infoArray['password'];

            /** @var Student|null $student */
            $student = $students[$studentId] ?? null;

            if($student === null) {
                continue;
            }

            $this->messageBus->dispatch(
                new CreateOrUpdateLmsStudentInfoMessage($student->getId(), $request->lms->getId(), $username, $password)
            );
        }
    }

    private function configureReader(Reader $reader): void {
        $reader->setHeaderOffset(0);
        $reader->setDelimiter(',');
        $reader->setEscape('');
    }
}