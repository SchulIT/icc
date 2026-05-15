<?php

namespace App\Display;

use App\Exam\Entity\ExamSupervision;
use App\Common\Entity\Grade;
use App\Common\Entity\Teacher;
use App\Common\Entity\Tuition;
use App\Framework\Grouping\Grouper;
use App\Common\Grouping\SubstitutionGradeGroup;
use App\Substitution\Grouping\SubstitutionGradeStrategy;
use App\Substitution\Grouping\SubstitutionTeacherGroup;
use App\Substitution\Grouping\SubstitutionTeacherStrategy;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Substitution\Repository\SubstitutionRepositoryInterface;
use App\Display\Sorting\DisplayGroupStrategy;
use App\Display\Sorting\DisplayViewItemStrategy;
use App\Framework\Sorting\Sorter;
use DateTime;

class DisplayHelper {

    public function __construct(private SubstitutionRepositoryInterface $substitutionRepository, private ExamRepositoryInterface $examRepository, private Grouper $grouper, private Sorter $sorter)
    {
    }

    /**
     * @param DateTime $dateTime
     * @return GradeGroup[]
     */
    public function getStudentsItems(DateTime $dateTime): array {
        /** @var GradeGroup[] $groups */
        $groups = [ ];

        $substitutions = $this->substitutionRepository->findAllByDate($dateTime, true);
        /** @var SubstitutionGradeGroup[] $substitutionGroups */
        $substitutionGroups = $this->grouper->group($substitutions, SubstitutionGradeStrategy::class);

        foreach($substitutionGroups as $group) {
            $key = $group->getGrade()->getId();

            if(!array_key_exists($key, $groups)) {
                $groups[$key] = new GradeGroup($group->getGrade());
            }

            foreach($group->getSubstitutions() as $substitution) {
                $groups[$key]->addItem(new SubstitutionViewItem($substitution));
            }
        }

        $exams = $this->examRepository->findAllByDate($dateTime);

        foreach($exams as $exam) {
            /** @var Tuition $tuition */
            foreach($exam->getTuitions() as $tuition) {
                /** @var Grade $grade */
                foreach($tuition->getStudyGroup()->getGrades() as $grade) {
                    $key = $grade->getId();

                    if(!array_key_exists($key, $groups)) {
                        $groups[$key] = new GradeGroup($grade);
                    }

                    $groups[$key]->addItem(new ExamViewItem($exam));
                }
            }
        }

        $this->sorter->sort($groups, DisplayGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, DisplayViewItemStrategy::class);

        return $groups;
    }

    /**
     * @param DateTime $dateTime
     * @return TeacherGroup[]
     */
    public function getTeachersItems(DateTime $dateTime): array {
        /** @var TeacherGroup[] $groups */
        $groups = [ ] ;

        $substitutions = $this->substitutionRepository->findAllByDate($dateTime, false);
        /** @var SubstitutionTeacherGroup[] $substitutionGroups */
        $substitutionGroups = $this->grouper->group($substitutions, SubstitutionTeacherStrategy::class);

        foreach($substitutionGroups as $group) {
            $key = $group->getTeacher()->getId();

            if(!array_key_exists($key, $groups)) {
                $groups[$key] = new TeacherGroup($group->getTeacher());
            }

            foreach($group->getSubstitutions() as $substitution) {
                $groups[$key]->addItem(new SubstitutionViewItem($substitution));
            }
        }

        $exams = $this->examRepository->findAllByDate($dateTime);

        foreach($exams as $exam) {
            /** @var Tuition $tuition */
            foreach($exam->getTuitions() as $tuition) {
                foreach($tuition->getTeachers() as $teacher) {
                    $key = $teacher->getId();

                    if(!array_key_exists($key, $groups)) {
                        $groups[$key] = new TeacherGroup($teacher);
                    }

                    $groups[$key]->addItem(new ExamViewItem($exam));
                }
            }

            /** @var ExamSupervision $supervision */
            foreach($exam->getSupervisions() as $supervision) {
                $key = $supervision->getTeacher()->getId();

                if(!array_key_exists($key, $groups)) {
                    $groups[$key] = new TeacherGroup($supervision->getTeacher());
                }

                $groups[$key]->addItem(new ExamSupervisionViewItem($supervision));
            }
        }

        $this->sorter->sort($groups, DisplayGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, DisplayViewItemStrategy::class);

        return $groups;
    }
}