<?php

namespace App\Display;

use App\Entity\ExamSupervision;
use App\Entity\Grade;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Grouping\Grouper;
use App\Grouping\SubstitutionGradeGroup;
use App\Grouping\SubstitutionGradeStrategy;
use App\Grouping\SubstitutionTeacherGroup;
use App\Grouping\SubstitutionTeacherStrategy;
use App\Repository\ExamRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Sorting\DisplayGroupStrategy;
use App\Sorting\DisplayViewItemStrategy;
use App\Sorting\Sorter;
use DateTime;

class DisplayHelper {

    public function __construct(private SubstitutionRepositoryInterface $substitutionRepository, private ExamRepositoryInterface $examRepository, private Grouper $grouper, private Sorter $sorter)
    {
    }

    public function getStudentsItems(DateTime $dateTime) {
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

    public function getTeachersItems(DateTime $dateTime) {
        /** @var TeacherGroup[] $groups */
        $groups = [ ];

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