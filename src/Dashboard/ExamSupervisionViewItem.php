<?php

namespace App\Dashboard;

class ExamSupervisionViewItem extends ExamViewItem {

    public function getBlockName(): string {
        return 'invigilator';
    }
}