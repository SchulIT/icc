<?php

namespace App\Dashboard;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Settings\DashboardSettings;
use App\Utils\ArrayUtils;
use InvalidArgumentException;

/**
 * Applies an algorithm in order to reduce the amount of items of one lesson to one.
 */
class DashboardViewCollapseHelper {

    public function __construct(private DashboardSettings $settings)
    {
    }

    public function collapseView(DashboardView $view, Teacher|Student|null $teacherOrStudent): void {
        foreach($view->getLessons() as $lesson) {
            $this->collapseLesson($lesson, $view, $teacherOrStudent);
        }

        // Post-validation
        $this->validateTimetableSupervisionsAndExamSupervisions($view);

        // Sort mentions and exams
        $this->sortMentions($view);
        $this->sortExams($view);
    }

    private function sortMentions(DashboardView $view): void {
        $mentions = $view->getSubstitutionMentions();
        $view->clearSubstitutionMentions();

        usort($mentions, fn(SubstitutionViewItem $viewItemA, SubstitutionViewItem $viewItemB) => $viewItemA->getSubstitution()->getLessonStart() - $viewItemB->getSubstitution()->getLessonEnd());

        foreach($mentions as $mention) {
            $view->addSubstitutonMention($mention);
        }
    }

    private function sortExams(DashboardView $view): void {
        $exams = $view->getExams();
        $view->clearExams();

        usort($exams, fn(ExamViewItem $examA, ExamViewItem $examB) => $examA->getExam()->getLessonStart() - $examB->getExam()->getLessonStart());

        foreach($exams as $exam) {
            $view->addExam($exam);
        }
    }

    private function validateTimetableSupervisionsAndExamSupervisions(DashboardView $view): void {
        $lessonNumbers = $view->getLessonNumbers();

        foreach($lessonNumbers as $lessonNumber) {
            $lesson = $view->getLesson($lessonNumber, true);

            if($lesson === null) {
                continue;
            }

            $lessonBefore = $view->getLesson($lessonNumber - 1, false);
            $lessonAfter = $view->getLesson($lessonNumber, false);

            if($lessonBefore === null || $lessonAfter === null) {
                continue;
            }

            // is supervision?
            $isSupervision = count(ArrayUtils::filterByType($lesson->getItems(), SupervisionViewItem::class)) > 0;
            $isExamSupervisionBefore = count(ArrayUtils::filterByType($lessonBefore->getItems(), ExamSupervisionViewItem::class)) > 0;
            $isExamSupervisionAfter = count(ArrayUtils::filterByType($lessonAfter->getItems(), ExamSupervisionViewItem::class)) > 0;

            if($isSupervision === true && $isExamSupervisionBefore === true && $isExamSupervisionAfter === true) {
                $lesson->setWarning();
            }
        }
    }

    private function collapseLesson(DashboardLesson $lesson, DashboardView $view, Teacher|Student|null $teacherOrStudent): void {
        // Merge supervisions
        $this->mergeExamSupervisions($lesson);

        // Add exams because they may not cause any troubles
        $this->addExamsToView($lesson, $view);

        // Store all items
        $originalItems = $lesson->getItems();

        // ... and all items as we will re-add them
        $lesson->clearItems();

        // STEP 1: TIMETABLE LESSONS
        /** @var TimetableLessonViewItem[] $timetableLessons */
        $timetableLessons = ArrayUtils::filterByType($originalItems, TimetableLessonViewItem::class);
        $timetableCount = count($timetableLessons);
        $freeTimetableLessons = ArrayUtils::filterByType($originalItems, FreeLessonView::class);
        $mergeTimetableLessons = false;

        if($timetableCount === 1 && $timetableLessons[0]->getLesson() !== null) {
            if(count($freeTimetableLessons) > 0) {
                $lesson->addItem($freeTimetableLessons[0]);
            } else {
                $lesson->addItem($timetableLessons[0]);
            }
        } else if($timetableCount > 1) {
            if($this->canMergeTimetableLessons($timetableLessons)) {
                $mergeTimetableLessons = true;
                $lesson->addItem($this->mergeTimetableLessons($timetableLessons));
            } else {
                $lesson->setWarning();
                $lesson->replaceItems($originalItems);
                return;
            }
        }

        // STEP 2: SUPERVISIONS
        /** @var SupervisionViewItem[] $supervisions */
        $supervisions = ArrayUtils::filterByType($originalItems, SupervisionViewItem::class);
        $supervisionCount = count($supervisions);

        if($supervisionCount === 1) {
            $lesson->addItem($supervisions[0]);
        } else if($supervisionCount > 1) {
            $lesson->setWarning();
            $lesson->replaceItems($originalItems);
            return;
        }

        // STEP 3: SUBSTITUTIONS
        /** @var SubstitutionViewItem[] $originalSubstitutions */
        $originalSubstitutions = ArrayUtils::filterByType($originalItems, SubstitutionViewItem::class);

        // Classify
        $substitutions = [ ];
        $substitutionMentions = [ ];

        foreach($originalSubstitutions as $substitution) {
            if($teacherOrStudent instanceof Teacher && $this->isMentionedInSubstitution($substitution, $teacherOrStudent) === true) {
                $substitutionMentions[] = $substitution;
            }

            if (!$teacherOrStudent instanceof Teacher || $this->onlyMentionedInSubstitution($substitution, $teacherOrStudent) === false) {
                $substitutions[] = $substitution;
            } else {
                $substitutionMentions[] = $substitution;
            }
        }

        // Further classication
        /** @var SubstitutionViewItem[] $additionalSubstitutions */
        $additionalSubstitutions = array_values(array_filter($substitutions, fn(SubstitutionViewItem $viewItem) => $this->isAdditionalSubstitution($viewItem, $teacherOrStudent)));
        /** @var SubstitutionViewItem[] $removableSubstitutions */
        $removableSubstitutions = array_values(array_filter($substitutions, fn(SubstitutionViewItem $viewItem) => $this->isRemovableSubstitution($viewItem, $teacherOrStudent)));
        /** @var SubstitutionViewItem[] $defaultSubstitutions */
        $defaultSubstitutions = array_values(array_filter($substitutions, fn(SubstitutionViewItem $viewItem) => $this->isDefault($viewItem, $teacherOrStudent)));

        $defaultSubstitutionsCount = $this->countDefaultSubstitutions($defaultSubstitutions);

        if(count($removableSubstitutions) > 1 || $defaultSubstitutionsCount > 1) {
            $lesson->setWarning();
            $lesson->replaceItems($originalItems);
            return;
        }

        foreach($removableSubstitutions as $substitution) {
            if($mergeTimetableLessons === false) {
                $lesson->clearItems();
            }
            $lesson->addItem($substitution);
        }

        if($defaultSubstitutionsCount > 0) {
            if($mergeTimetableLessons === false) {
                $lesson->clearItems();
            }
            $mergedDefaultSubstitutions = $this->mergeSubstitutions($defaultSubstitutions);

            foreach ($mergedDefaultSubstitutions as $substitution) {
                $lesson->addItem($substitution);
            }
        }

        // Add Non-Replacing substitutions
        foreach($additionalSubstitutions as $substitution) {
            if($lesson->hasItem($substitution) === false) {
                $lesson->addItem($substitution);
            }
        }

        // STEP 4: EXAM SUPERVISION
        /** @var ExamSupervisionViewItem[] $examSupervisions */
        $examSupervisions = ArrayUtils::filterByType($originalItems, ExamSupervisionViewItem::class);
        $examSupervisionCount = count($examSupervisions);

        if($examSupervisionCount > 1) {
            $lesson->setWarning();
            $lesson->replaceItems($originalItems);
            return;
        } else if($examSupervisionCount === 1) {
            $collision = false;

            foreach($lesson->getItems() as $item) {
                if(!($item instanceof SubstitutionViewItem) || $this->isDefault($item, $teacherOrStudent)) {
                    $collision = true;
                }
            }

            if($collision === true) {
                $lesson->setWarning();
                $lesson->replaceItems($originalItems);
                return;
            } else {
                $lesson->clearItems();
                $lesson->addItem($examSupervisions[0]);
            }
        }

        // EVERYTHING WORKED SO FAR: move certain items to view
        foreach($substitutionMentions as $mention) {
            $view->addSubstitutonMention($mention);
        }

        foreach(ArrayUtils::filterByType($originalItems, ExamViewItem::class) as $examItem) {
            $view->addExam($examItem);
        }

        // ADD FREE HOURS
        if(count($lesson->getItems()) === 0) {
            $lesson->addItem(new TimetableLessonViewItem(null, [ ], null));
        }

        // ADD ALL ITEMS THAT HAVE NOT BEEN TAKE CONCIDERATION
        $consideredTypes = [
            ExamViewItem::class,
            SubstitutionViewItem::class,
            SupervisionViewItem::class,
            TimetableLessonViewItem::class,
            ExamSupervisionViewItem::class,
            FreeLessonView::class
        ];

        foreach($originalItems as $originalItem) {
            if(!in_array($originalItem::class, $consideredTypes)) {
                $lesson->addItem($originalItem);
            }
        }
    }

    /**
     * @param TimetableLessonViewItem[] $lessonViews
     * @return boolean
     */
    private function canMergeTimetableLessons(array $lessonViews): bool {
        $rooms = [ ];
        $locations = [ ];

        foreach($lessonViews as $view) {
            $lesson = $view->getLesson();

            if($lesson->getRoom() !== null) {
                $rooms[] = $lesson->getRoom()->getId();
            }

            if($lesson->getLocation() !== null) {
                $locations[] = $lesson->getLocation();
            }
        }

        $distinctRooms = array_unique($rooms);
        $distinctLocations = array_unique($locations);

        if(count($distinctRooms) === 0 && count($distinctLocations) === 0) {
            return true;
        }

        if(count($distinctRooms) === 1 && count($distinctLocations) === 0) {
            return true;
        }

        if(count($distinctRooms) === 0 && count($distinctLocations) === 1) {
            return true;
        }

        return false;
    }

    /**
     * @param TimetableLessonViewItem[] $lessonViews
     * @return TimetableLessonViewItem
     */
    private function mergeTimetableLessons(array $lessonViews) {
        if(count($lessonViews) === 0) {
            throw new InvalidArgumentException('$lessonView must at least contain one element.');
        }

        $absentGroups = [ ];

        foreach($lessonViews as $lessonView) {
            $absentGroups = array_merge($lessonView->getAbsentStudentGroups());
        }

        $firstView = array_shift($lessonViews);

        $view = new TimetableLessonViewItem($firstView->getLesson(), $absentGroups, $firstView->getAbsenceLesson());

        foreach($lessonViews as $lessonView) {
            $view->addAdditionalLesson($lessonView->getLesson());
        }

        return $view;
    }

    private function addExamsToView(DashboardLesson $lesson, DashboardView $view) {
        $items = $lesson->getItems();
        $lesson->clearItems();

        foreach($items as $item) {
            if($item instanceof ExamViewItem) {
                $view->addExam($item);
            } else {
                $lesson->addItem($item);
            }
        }
    }

    private function mergeExamSupervisions(DashboardLesson $lesson): void {
        $items = $lesson->getItems();
        $supervisions = [ ];

        $lesson->clearItems();

        foreach($items as $item) {
            if($item instanceof ExamSupervisionViewItem) {
                $supervisions[] = $item;
            } else {
                $lesson->addItem($item);
            }
        }

        /** @var ExamSupervisionViewItem[] $merged */
        $merged = [ ];

        foreach($supervisions as $supervision) {
            $firstExam = $supervision->getFirst();

            if($firstExam === null) {
                continue;
            }

            $isMerged = false;

            foreach($merged as $mergedSupervisionItem) {
                $firstMergedExam = $mergedSupervisionItem->getFirst();

                if($firstMergedExam !== null && $firstExam->getRoom() === $firstMergedExam->getRoom()) {
                    $isMerged = true;
                    $mergedSupervisionItem->addExam($firstExam);
                }
            }

            if($isMerged === false) {
                $merged[] = $supervision;
            }
        }

        foreach($merged as $item) {
            $lesson->addItem($item);
        }
    }

    /**
     * @param SubstitutionViewItem[] $substitutions
     * @return SubstitutionViewItem[]
     */
    private function mergeSubstitutions(array $substitutions): array {
        /** @var SubstitutionViewItem[] $merged */
        $merged = [ ];

        foreach($substitutions as $substitutionViewItem) {
            $substitution = $substitutionViewItem->getSubstitution();
            $isMerged = false;

            foreach($merged as $mergedViewItem) {
                $mergedSubstitution = $mergedViewItem->getSubstitution();

                if($substitution->getType() === $mergedSubstitution->getType()
                    && $substitution->getSubject() === $mergedSubstitution->getSubject()
                    && $substitution->getRemark() === $mergedSubstitution->getRemark()
                    && ArrayUtils::areEqual($substitution->getRooms(), $mergedSubstitution->getRooms())
                    && $substitution->getRoomName() === $mergedSubstitution->getRoomName()) {

                    // merge study groups
                    foreach($substitution->getStudyGroups() as $studyGroup) {
                        if($mergedSubstitution->getStudyGroups()->contains($studyGroup) === false) {
                            $mergedSubstitution->addStudyGroup($studyGroup);
                        }
                    }

                    foreach ($substitution->getReplacementStudyGroups() as $studyGroup) {
                        if($mergedSubstitution->getReplacementStudyGroups()->contains($studyGroup) === false) {
                            $mergedSubstitution->addReplacementStudyGroup($studyGroup);
                        }
                    }

                    // merge teachers
                    foreach($substitution->getTeachers() as $teacher) {
                        if($mergedSubstitution->getTeachers()->contains($teacher) === false) {
                            $mergedSubstitution->addTeacher($teacher);
                        }
                    }
                    foreach($substitution->getReplacementTeachers() as $teacher) {
                        if($mergedSubstitution->getReplacementTeachers()->contains($teacher) === false) {
                            $mergedSubstitution->addReplacementTeacher($teacher);
                        }
                    }

                    $isMerged = true;
                }
            }

            if($isMerged === false) {
                $clonedSubstitution = $substitution->clone(); // Somehow, clone $substitution does not work (when renameing clone() to __clone())
                $item = new SubstitutionViewItem($clonedSubstitution, false, $substitutionViewItem->getStudents(), $substitutionViewItem->getAbsentStudentGroups(), $substitutionViewItem->getTimetableLesson(), $substitutionViewItem->getAbsenceLesson());
                $merged[] = $item;
            }
        }

        return $merged;
    }

    /**
     * @param SubstitutionViewItem[] $defaultSubstitutions
     * @return int
     */
    private function countDefaultSubstitutions(array $defaultSubstitutions) {
        $count = count($defaultSubstitutions);

        for($i = 0; $i < count($defaultSubstitutions); $i++) {
            for($j = $i + 1; $j < count($defaultSubstitutions); $j++) {
                $leftSubstitution = $defaultSubstitutions[$i]->getSubstitution();
                $rightSubstitution = $defaultSubstitutions[$j]->getSubstitution();

                // If subject and room are same: remove count by 1
                if($leftSubstitution->getSubject() === $rightSubstitution->getSubject() && ArrayUtils::areEqual($leftSubstitution->getRooms(), $rightSubstitution->getRooms())) {
                    $count--;
                }
            }
        }

        return $count;
    }

    private function isRemovableSubstitution(SubstitutionViewItem $viewItem, Teacher|Student|null $teacherOrStudent): bool {
        if($this->isAdditionalSubstitution($viewItem, $teacherOrStudent)) {
            return false;
        }

        if(in_array($viewItem->getSubstitution()->getType(), $this->settings->getRemovableSubstitutionTypes())) {
            return true;
        }

        $substitution = $viewItem->getSubstitution();

        if($teacherOrStudent instanceof Teacher) {
            return $substitution->getTeachers()->contains($teacherOrStudent) && $substitution->getReplacementTeachers()->contains($teacherOrStudent) === false;
        }

        return false;
    }

    private function isAdditionalSubstitution(SubstitutionViewItem $viewItem, Teacher|Student|null $teacherOrStudent): bool {
        if($teacherOrStudent instanceof Teacher) {
            return in_array($viewItem->getSubstitution()->getType(), $this->settings->getAdditionalSubstitutionTypes());
        }

        return false;
    }

    private function onlyMentionedInSubstitution(SubstitutionViewItem $viewItem, Teacher $teacher): bool {
        $substitution = $viewItem->getSubstitution();

        foreach($substitution->getTeachers() as $substitutionTeacher) {
            if($substitutionTeacher->getId() === $teacher->getId()) {
                return false;
            }
        }

        foreach($substitution->getReplacementTeachers() as $substitutionTeacher) {
            if($substitutionTeacher->getId() === $teacher->getId()) {
                return false;
            }
        }

        return true;
    }

    private function isMentionedInSubstitution(SubstitutionViewItem $viewItem, Teacher $teacher): bool {
        return !empty($viewItem->getSubstitution()->getRemark()) && preg_match('~\W*' . $teacher->getAcronym() . '\W*~', $viewItem->getSubstitution()->getRemark());
    }

    private function isDefault(SubstitutionViewItem $viewItem, Teacher|Student|null $teacherOrStudent): bool {
        return $this->isRemovableSubstitution($viewItem, $teacherOrStudent) === false && $this->isAdditionalSubstitution($viewItem, $teacherOrStudent) === false;
    }
}