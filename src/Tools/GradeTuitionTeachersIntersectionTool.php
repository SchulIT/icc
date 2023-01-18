<?php

namespace App\Tools;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;

/**
 * This tool computes the intersection of teachers between two grades.
 */
class GradeTuitionTeachersIntersectionTool {
    public function __construct(private readonly TuitionRepositoryInterface $tuitionRepository, private readonly Sorter $sorter) {

    }

    /**
     * @return GradeTuitionTeachersIntersection[]
     */
    public function computeIntersections(GradeTuitionTeachersIntersectionInput $input): array {
        $subjectIds = array_map(fn(Subject $subject) => $subject->getId(), $input->subjects);
        $intersections = [ ];

        $this->sorter->sort($input->leftGrades, GradeNameStrategy::class);
        $this->sorter->sort($input->rightGrades, GradeNameStrategy::class);

        foreach($input->leftGrades as $leftGrade) {
            foreach($input->rightGrades as $rightGrade) {
                if($leftGrade->getId() === $rightGrade->getId()) {
                    continue;
                }

                $leftTeachers = $this->getTeachersForGrade($leftGrade, $subjectIds, $input->section);
                $rightTeachers = $this->getTeachersForGrade($rightGrade, $subjectIds, $input->section);

                $intersections[] = new GradeTuitionTeachersIntersection($leftGrade, $rightGrade, array_intersect($leftTeachers, $rightTeachers));
            }
        }

        return $intersections;
    }


    /**
     * Returns all teachers for the given grade, subjects (in form of subject IDs) and section.
     *
     * @param Grade $grade
     * @param array $subjectIds
     * @param Section $section
     * @return string[] Teachers (acronyms only)
     */
    private function getTeachersForGrade(Grade $grade, array $subjectIds, Section $section): array {
        $tuitions = $this->tuitionRepository->findAllByGrades([$grade], $section, true);
        $tuitions = array_filter($tuitions, fn(Tuition $tuition) => $tuition->getSubject() !== null && in_array($tuition->getSubject()->getId(), $subjectIds));

        $teachers = [];

        foreach($tuitions as $tuition) {
            foreach($tuition->getTeachers() as $teacher) {
                if(!in_array($teacher->getAcronym(), $teachers)) {
                    $teachers[] = $teacher->getAcronym();
                }
            }
        }

        // Add grade teachers (just in case...)
        /** @var GradeTeacher $gradeTeacher */
        foreach($grade->getTeachers() as $gradeTeacher) {
            if($gradeTeacher->getSection() === $section && !in_array($gradeTeacher->getTeacher()->getAcronym(), $teachers)) {
                $teachers[] = $gradeTeacher->getTeacher()->getAcronym();
            }
        }

        return $teachers;
    }
}