<?php

namespace App\Dashboard;

use App\Entity\Tuition;

/**
 * Tries to merge dashboard items
 */
class DashboardViewMergeHelper {
    public function mergeView(DashboardView $view) {
        /**
         * @var int $lesson
         * @var AbstractViewItem[] $items
         */
        foreach($view->getLessons() as $lesson) {
            $items = $view->getItems($lesson);
            foreach($items as $item) {
                if($item instanceof LessonViewItem) {
                    $examView = $this->findTuitionExam($item->getLesson()->getTuition(), $items);

                    if($examView !== null) {
                        $this->mergeLessonAndExam($item, $examView);
                    }

                    foreach($items as $substitutionItem) {
                        if($substitutionItem instanceof SubstitutionViewItem) {
                            $item->hide();
                        }
                    }
                }
            }
        }
    }

    private function mergeLessonAndExam(LessonViewItem $lessonViewItem, ExamViewItem $examViewItem) {
        $lessonViewItem->addMergedItem($examViewItem);
        $examViewItem->hide();
    }

    /**
     * Finds an exam item that belongs to the given tuition
     * @param Tuition $tuition
     * @param AbstractViewItem[] $items
     * @return ExamViewItem|null
     */
    private function findTuitionExam(Tuition $tuition, array $items): ?ExamViewItem {
        foreach($items as $item) {
            if($item instanceof ExamViewItem) {
                $tuitionIds = $item->getExam()->getTuitions()->map(function(Tuition $tuition) { return $tuition->getId(); })->toArray();

                if(in_array($tuition->getId(), $tuitionIds)) {
                    return $item;
                }
            }
        }

        return null;
    }
}