<?php

namespace App\Export;

use App\Csv\CsvHelper;
use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Entity\Teacher;
use App\Repository\TeacherRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeacherCsvExporter {

    private const Filename = 'teachers.csv';

    private $teacherRepository;
    private $csvHelper;
    private $translator;

    public function __construct(TeacherRepositoryInterface $teacherRepository, CsvHelper $csvHelper, TranslatorInterface $translator) {
        $this->teacherRepository = $teacherRepository;
        $this->csvHelper = $csvHelper;
        $this->translator = $translator;
    }

    /**
     * @return string[][]
     */
    public function getRows(): array {
        $teachers = $this->teacherRepository->findAll();

        $maxGrades = max(array_map(function(Teacher $teacher) {
            return $teacher->getGrades()->filter(function(GradeTeacher $gradeTeacher) {
                return $gradeTeacher->getType()->equals(GradeTeacherType::Primary());
            });
        }, $teachers));

        $maxSubstitutionalGrades = max(array_map(function(Teacher $teacher) {
            return $teacher->getGrades()->filter(function(GradeTeacher $gradeTeacher) {
                return $gradeTeacher->getType()->equals(GradeTeacherType::Substitutional());
            });
        }, $teachers));

        $maxSubjects = max(array_map(function(Teacher $teacher) {
            return count($teacher->getSubjects());
        }, $teachers));

        $rows = [ ];

        // header
        $header = [
            $this->translator->trans('label.acronym'),
            $this->translator->trans('label.name')
        ];

        for($i = 1; $i <= $maxGrades; $i++) {
            $header[] = $this->translator->trans('teachers.export.headings.grade', [
                '%i%' => $i
            ]);
        }

        for($i = 1; $i <= $maxSubstitutionalGrades; $i++) {
            $header[] = $this->translator->trans('teachers.export.headings.grade_substitutional', [
                '%i%' => $i
            ]);
        }

        for($i = 1; $i <= $maxSubjects; $i++) {
            $header[] = $this->translator->trans('teachers.export.headings.subject', [
                '%i%' => $i
            ]);
        }

        $rows[] = $header;

        // rows
        foreach($teachers as $teacher) {
            $row = [
                $teacher->getAcronym(),
                $teacher->getLastname(),
            ];

            foreach ($teacher->getGrades() as $grade) {
                $row[] = $grade->getName();
            }

            /** @var GradeTeacher[] $primaryGrades */
            $primaryGrades = $teacher->getGrades()->filter(function(GradeTeacher $gradeTeacher) {
                return $gradeTeacher->getType()->equals(GradeTeacherType::Primary());
            });

            foreach($primaryGrades as $gradeTeacher) {
                $row[] = $gradeTeacher->getGrade()->getName();
            }

            for($i = count($primaryGrades) + 1; $i <= count($maxGrades); $i++) {
                $row[] = '';
            }

            /** @var GradeTeacher[] $substitutionalGrades */
            $substitutionalGrades = $teacher->getGrades()->filter(function(GradeTeacher $gradeTeacher) {
                return $gradeTeacher->getType()->equals(GradeTeacherType::Substitutional());
            });

            foreach($substitutionalGrades as $gradeTeacher) {
                $row[] = $gradeTeacher->getGrade()->getName();
            }

            for($i = count($substitutionalGrades) + 1; $i <= count($maxSubstitutionalGrades); $i++) {
                $row[] = '';
            }

            foreach($teacher->getSubjects() as $subject) {
                $row[] = $subject->getAbbreviation();
            }

            for($i = count($teacher->getSubjects()) + 1; $i <= count($maxSubjects); $i++) {
                $row[] = '';
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function getCsvResponse(): Response {
        $rows = $this->getRows();

        return $this->csvHelper->getCsvResponse(static::Filename, $rows);
    }
}