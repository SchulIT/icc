<?php

namespace App\Dashboard;

use App\Entity\Exam;

class ExamInvigilatorViewItem extends ExamViewItem {

    public function getBlockName(): string {
        return 'invigilator';
    }
}