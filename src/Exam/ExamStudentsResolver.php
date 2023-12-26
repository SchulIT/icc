<?php

namespace App\Exam;

use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\ImportSettings;
use App\Utils\CollectionUtils;
use DateTime;

class ExamStudentsResolver {

    public function __construct(private readonly ImportSettings $importSettings, private readonly SectionResolverInterface $sectionResolver) {

    }

    private function computeRules(DateTime $dateTime): array {
        $rules = [ ];
        $currentSection = $this->sectionResolver->getSectionForDate($dateTime);

        foreach($this->importSettings->getExamRules() as $rule) {
            $grades = array_map('trim', explode(',', $rule['grades']));
            $sections = array_map('trim', explode(',', $rule['sections']));
            $types = array_map('trim',  explode(',', $rule['types']));

            foreach($sections as $section) {
                if($section == $currentSection->getNumber()) {
                    foreach($grades as $grade) {
                        $rules[$grade] = $types;
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * @param Exam $exam
     * @param ExamStudent[] $givenStudents
     * @return ExamStudent[]
     */
    public function resolveExamStudentsFromGivenStudents(Exam $exam, array $givenStudents): array {
        $rules = $this->computeRules($exam->getDate());
        $section = $this->sectionResolver->getSectionForDate($exam->getDate());

        $collisions = [ ];

        foreach($givenStudents as $examStudent) {
            $student = $examStudent->getStudent();
            $grade = $student->getGrade($section);

            foreach($exam->getTuitions() as $tuition) {
                foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                    if($membership->getStudent()->getId() !== $student->getId()) {
                        continue;
                    }

                    // Student attends tuition
                    if ($grade !== null && array_key_exists($grade->getName(), $rules)) {
                        // Method A: RULE MATCHES
                        if(in_array($membership->getType(), $rules[$grade->getName()])) {
                            if($examStudent->getTuition() !== null) {
                                $collisions[] = $examStudent;
                            } else {
                                $examStudent->setTuition($tuition);
                            }
                        }
                    } else {
                        // METHOD B: ATTENDANCE
                        if($examStudent->getTuition() !== null) {
                            $collisions[] = $examStudent;
                        } else {
                            $examStudent->setTuition($tuition);
                        }
                    }
                }
            }
        }

        foreach($collisions as $examStudent) {
            $examStudent->setTuition(null);
        }

        return $givenStudents;
    }

    /**
     * @param Exam $exam
     * @return ExamStudent[]
     */
    public function resolveExamStudentsFromMembership(Exam $exam): array {
        $students = [ ];

        foreach($exam->getTuitions() as $tuition) {
            foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                $student = $membership->getStudent();

                if(!array_key_exists($student->getId(), $students)) {
                    $students[$student->getId()] = (new ExamStudent())->setExam($exam)->setStudent($student)->setTuition($tuition);
                } else {
                    $students[$student->getId()]->setTuition(null); // COLLISION
                }
            }
        }

        return $students;
    }

    /**
     * @param Exam $exam
     * @return ExamStudent[]
     */
    public function resolveExamStudentsByRules(Exam $exam): array {
        $rules = $this->computeRules($exam->getDate());
        $section = $this->sectionResolver->getSectionForDate($exam->getDate());
        $students = [ ];

        /** @var Tuition $tuition */
        foreach($exam->getTuitions() as $tuition) {
            /** @var StudyGroupMembership $membership */
            foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                $student = $membership->getStudent();
                $grade = $student->getGrade($section);

                if($grade !== null && array_key_exists($grade->getName(), $rules) && in_array($membership->getType(), $rules[$grade->getName()])) {
                    if(!array_key_exists($student->getId(), $students)) {
                        $students[$student->getId()] = (new ExamStudent())->setStudent($student)->setExam($exam)->setTuition($tuition);
                    } else {
                        $students[$student->getId()]->setTuition(null); // COLLISION
                    }
                }
            }
        }

        return array_values($students);
    }

    /**
     * @param Exam $exam
     * @param ExamStudent[] $examStudents
     * @return void
     */
    public function setExamStudents(Exam $exam, iterable $examStudents): void {
        CollectionUtils::synchronize(
            $exam->getStudents(),
            $examStudents,
            fn(ExamStudent $examStudent) => $examStudent->getStudent()->getId(),
            fn(ExamStudent $existingStudent, ExamStudent $targetStudent) => $existingStudent->setTuition($targetStudent->getTuition())->setExam($exam)
        );
    }
}