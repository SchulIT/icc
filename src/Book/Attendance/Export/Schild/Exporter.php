<?php

namespace App\Book\Attendance\Export\Schild;

use App\Book\Student\StudentInfoResolver;
use App\Entity\Student as StudentEntity;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class Exporter {
    public function __construct(
        private ValidatorInterface $validator,
        private StudentRepositoryInterface $studentRepository,
        private StudentInfoResolver $studentInfoResolver,
        private SectionRepositoryInterface $sectionRepository
    ) {

    }

    public function exportBulk(BulkRequest $bulkRequest): BulkResponse {
        $bulkResponse = new BulkResponse();

        foreach($bulkRequest->requests as $request) {
            $bulkResponse->responses[] = $this->export($request);
        }

        return $bulkResponse;
    }

    public function export(Request $request): Response|ErrorResponse {
        $violations = $this->validator->validate($request);

        if (count($violations) > 0) {
            return new ErrorResponse('Anfrage ungültig.');
        }

        $section = $this->sectionRepository->findOneByNumberAndYear($request->section, $request->year);

        if($section === null) {
            return new ErrorResponse(sprintf('Abschnitt %d/%d nicht gefunden.', $request->year, $request->section));
        }

        try {
            $student = $this->getStudent($request);
        } catch (StudentNotFoundException $e) {
            return new ErrorResponse(sprintf('Schülerin oder Schüler %s, %s (%s) nicht gefunden: %s', $request->lastname, $request->firstname, $request->birthday->format('d.m.Y'), $e->getReason()));
        }

        $response = new Response();
        $response->section = $request->section;
        $response->year = $request->year;
        $response->firstname = $request->firstname;
        $response->lastname = $request->lastname;
        $response->birthday = $request->birthday;

        $info = $this->studentInfoResolver->resolveStudentInfo($student, $section, includeEvents: true, untilDate: $request->untilDate);

        $response->absentLessons = $info->getAbsentLessonsCount();
        $response->notExcusedAbsentLessons = $info->getNotExcusedOrNotSetLessonsCount() + $info->getNotExcusedAbsentLessonsCount();

        return $response;
    }

    /**
     * @throws StudentNotFoundException
     */
    private function getStudent(Request $request): StudentEntity {
        $students = $this->studentRepository->findAllByNameAndBirthday($request->firstname, $request->lastname, $request->birthday);

        if(count($students) === 0) {
            throw new StudentNotFoundException(StudentNotFoundException::NOT_FOUND);
        } else if(count($students) > 1) {
            throw new StudentNotFoundException(StudentNotFoundException::AMBIGUOUS);
        }

        return $students[0];
    }
}