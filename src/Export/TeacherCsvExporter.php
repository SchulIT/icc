<?php

namespace App\Export;

use App\Csv\CsvHelper;
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
            return count($teacher->getGrades());
        }, $teachers));

        $maxSubstitudeGrades = max(array_map(function(Teacher $teacher) {
            return count($teacher->getGradeSubstitutes());
        }, $teachers));

        $maxSubjects = max(array_map(function(Teacher $teacher) {
            return count($teacher->getSubjects());
        }, $teachers));

        $rows = [ ];

        // header
        $header = [
            $this->translator->trans('label.acronym'),
            $this->translator->trans('label.name'),
            $this->translator->trans('label.email')
        ];

        for($i = 1; $i <= $maxGrades; $i++) {
            $header[] = $this->translator->trans('teachers.export.headings.grade', [
                '%i%' => $i
            ]);
        }

        for($i = 1; $i <= $maxSubstitudeGrades; $i++) {
            $header[] = $this->translator->trans('teachers.export.headings.grade_substitute', [
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
                $teacher->getEmail()
            ];

            foreach ($teacher->getGrades() as $grade) {
                $row[] = $grade->getName();
            }

            for($i = count($teacher->getGrades()) + 1; $i <= count($maxGrades); $i++) {
                $row[] = '';
            }

            foreach($teacher->getGradeSubstitutes() as $grade) {
                $row[] = $grade->getName();
            }

            for($i = count($teacher->getGradeSubstitutes()) + 1; $i <= count($maxSubstitudeGrades); $i++) {
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