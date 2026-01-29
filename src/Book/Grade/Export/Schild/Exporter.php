<?php

namespace App\Book\Grade\Export\Schild;

use App\Book\Student\StudentInfoResolver;
use App\Entity\Student as StudentEntity;
use App\Entity\StudyGroupType;
use App\Entity\Tuition as TuitionEntity;
use App\Entity\TuitionGrade;
use App\Entity\TuitionGradeCategory;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionGradeCategoryRepositoryInterface;
use App\Repository\TuitionGradeRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class Exporter {

    public function __construct(private ValidatorInterface $validator,
                                private StudentRepositoryInterface $studentRepository,
                                private TuitionGradeRepositoryInterface $tuitionGradeRepository,
                                private TuitionRepositoryInterface $tuitionRepository,
                                private TuitionGradeCategoryRepositoryInterface $tuitionGradeCategoryRepository,
                                private StudentInfoResolver $infoResolver,
                                private SectionRepositoryInterface $sectionRepository) {

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

        try {
            $category = $this->getGradeCategory($request);
        } catch (GradeCategoryNotFoundException $e) {
            return new ErrorResponse(sprintf('Notenkategorie mit der ID %s nicht gefunden.', $request->grade));
        }

        $response = new Response();
        $response->section = $request->section;
        $response->year = $request->year;
        $response->firstname = $request->firstname;
        $response->lastname = $request->lastname;
        $response->birthday = $request->birthday;

        $tuitions = $this->tuitionRepository->findAllByStudents([$student], $section);
        $grades = $this->tuitionGradeRepository->findAllByStudent($student, $section);

        foreach($tuitions as $tuition) {
            $tuitionResponse = new Tuition();
            $tuitionResponse->subject = $tuition->getSubject()?->getAbbreviation();
            $tuitionResponse->course = $tuition->getStudyGroup()->getType() === StudyGroupType::Course ? $tuition->getName() : null;

            $tuitionResponse->grade = $this->getGradeOrNull($grades, $tuition, $category)?->getEncryptedGrade();

            $info = $this->infoResolver->resolveStudentInfo($student, $section, [$tuition]);

            $tuitionResponse->absentLessons = $info->getAbsentLessonsCount();
            $tuitionResponse->nonExcusedLessons = $info->getNotExcusedOrNotSetLessonsCount();

            foreach($tuition->getTeachers() as $teacher) {
                $tuitionResponseClone = clone $tuitionResponse;
                $tuitionResponseClone->teacher = $teacher->getAcronym();

                $response->tuitions[] = $tuitionResponseClone;
            }
        }

        return $response;
    }

    /**
     * @param TuitionGrade[] $grades
     * @param TuitionEntity $tuition
     * @param TuitionGradeCategory $category
     * @return TuitionGrade|null
     */
    private function getGradeOrNull(array $grades, TuitionEntity $tuition, TuitionGradeCategory $category): ?TuitionGrade {
        foreach($grades as $grade) {
            if($grade->getTuition() === $tuition && $grade->getCategory() === $category) {
                return $grade;
            }
        }

        return null;
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

    /**
     * @throws GradeCategoryNotFoundException
     */
    private function getGradeCategory(Request $request): TuitionGradeCategory {
        $category = $this->tuitionGradeCategoryRepository->findOneByUuid($request->grade);

        if($category === null) {
            throw new GradeCategoryNotFoundException();
        }

        return $category;
    }
}