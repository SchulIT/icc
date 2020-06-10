<?php

namespace App\Dashboard;

use App\Entity\Teacher;
use App\Settings\DashboardSettings;
use App\Utils\ArrayUtils;

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

        if($timetableCount === 1 && $timetableLessons[0]->getLesson() !== null) {
            $lesson->addItem($timetableLessons[0]);
        } else if($timetableCount > 1) {
            $lesson->setWarning();
            $lesson->replaceItems($originalItems);
            return;
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
        /** @var SubstitutionViewItem[] $substitutions */
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
        $additionalSubstitutions = array_filter($substitutions, [ $this, 'isAdditionalSubstitution']);
        $removableSubstitutions = array_filter($substitutions, [ $this, 'isRemovableSubstitution']);
        $defaultSubstitutions = array_filter($substitutions, [ $this, 'isDefault' ]);

        if(count($removableSubstitutions) > 1 || count($defaultSubstitutions) > 1) {
            $lesson->setWarning();
            $lesson->replaceItems($originalItems);
            return;
        }

        foreach($removableSubstitutions as $substitution) {
            $lesson->clearItems();
            $lesson->addItem($substitution);
        }

        foreach($defaultSubstitutions as $substitution) {
            $lesson->clearItems();
            $lesson->addItem($substitution);
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
                if(!($item instanceof SubstitutionViewItem) || $this->isDefault($item)) {
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
            ExamSupervisionViewItem::class
        ];

        foreach($originalItems as $originalItem) {
            if(!in_array(get_class($originalItem), $consideredTypes)) {
                $lesson->addItem($originalItem);
            }
        }
    }

    private function isRemovableSubstitution(SubstitutionViewItem $viewItem) {
        return in_array($viewItem->getSubstitution()->getType(), $this->settings->getRemovableSubstitutionTypes());
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

    private function isDefault(SubstitutionViewItem $viewItem) {
        return $this->isRemovableSubstitution($viewItem) === false && $this->isAdditionalSubstitution($viewItem) === false;
    }
}