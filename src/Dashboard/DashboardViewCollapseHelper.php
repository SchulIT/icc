<?php

namespace App\Dashboard;

use App\Entity\Teacher;
use App\Settings\DashboardSettings;
use App\Utils\ArrayUtils;
use InvalidArgumentException;

/**
 * Applies an algorithm in order to reduce the amount of items of one lesson to one.
 */
class DashboardViewCollapseHelper {

    private $settings;

    public function __construct(DashboardSettings $settings) {
        $this->settings = $settings;
    }

    public function collapseView(DashboardView $view, ?Teacher $teacher) {
        foreach($view->getLessons() as $lesson) {
            $this->collapseLesson($lesson, $view, $teacher);
        }

        // Post-validation
        $this->validateTimetableSupervisionsAndExamSupervisions($view);
    }

    private function validateTimetableSupervisionsAndExamSupervisions(DashboardView $view) {
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

    private function collapseLesson(DashboardLesson $lesson, DashboardView $view, ?Teacher $teacher): void {
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
            if ($teacher === null || $this->onlyMentionedInSubstitution($substitution, $teacher) === false) {
                $substitutions[] = $substitution;
            } else {
                $substitutionMentions[] = $substitution;
            }
        }

        // Further classication
        /** @var SubstitutionViewItem[] $additionalSubstitutions */
        $additionalSubstitutions = array_values(array_filter($substitutions, [ $this, 'isAdditionalSubstitution']));
        /** @var SubstitutionViewItem[] $removableSubstitutions */
        $removableSubstitutions = array_values(array_filter($substitutions, function(SubstitutionViewItem $viewItem) use ($teacher) {
            return $this->isRemovableSubstitution($viewItem, $teacher);
        }));
        /** @var SubstitutionViewItem[] $defaultSubstitutions */
        $defaultSubstitutions = array_values(array_filter($substitutions, function(SubstitutionViewItem $viewItem) use ($teacher) {
            return $this->isDefault($viewItem, $teacher);
        }));

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
            $lesson->addItem($substitution);
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
                if(!($item instanceof SubstitutionViewItem) || $this->isDefault($item, $teacher)) {
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
            $lesson->addItem(new TimetableLessonViewItem(null, [ ]));
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
            if(!in_array(get_class($originalItem), $consideredTypes)) {
                $lesson->addItem($originalItem);
            }
        }
    }

    /**
     * @param TimetableLessonViewItem[] $lessonViews
     * @return boolean
     */
    private function canMergeTimetableLessons(array $lessonViews) {
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

        $view = new TimetableLessonViewItem($firstView->getLesson(), $absentGroups);

        foreach($lessonViews as $lessonView) {
            $view->addAdditionalLesson($lessonView->getLesson());
        }

        return $view;
    }

    /**
     * @param SubstitutionViewItem[] $substitutions
     * @return SubstitutionViewItem[]
     */
    private function mergeSubstitutions(array $substitutions) {
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
                    && $substitution->getRoom() === $mergedSubstitution->getRoom()
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
                $item = new SubstitutionViewItem($clonedSubstitution, false);
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
                if($leftSubstitution->getSubject() === $rightSubstitution->getSubject() && $leftSubstitution->getRoom() === $rightSubstitution->getRoom()) {
                    $count--;
                }
            }
        }

        return $count;
    }

    private function isRemovableSubstitution(SubstitutionViewItem $viewItem, ?Teacher $teacher) {
        if(in_array($viewItem->getSubstitution()->getType(), $this->settings->getRemovableSubstitutionTypes())) {
            return true;
        }

        $substitution = $viewItem->getSubstitution();

        if($teacher !== null) {
            return $substitution->getTeachers()->contains($teacher) && $substitution->getReplacementTeachers()->contains($teacher) === false;
        }

        return false;
    }

    private function isAdditionalSubstitution(SubstitutionViewItem $viewItem) {
        return in_array($viewItem->getSubstitution()->getType(), $this->settings->getAdditionalSubstitutionTypes());
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

    private function isDefault(SubstitutionViewItem $viewItem, ?Teacher $teacher) {
        return $this->isRemovableSubstitution($viewItem, $teacher) === false && $this->isAdditionalSubstitution($viewItem) === false;
    }
}