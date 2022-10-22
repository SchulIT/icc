<?php

namespace App\Dashboard;

use App\Entity\TimetableSupervision;

class SupervisionViewItem extends AbstractViewItem {

    public function __construct(private TimetableSupervision $supervision)
    {
    }

    public function getSupervision(): TimetableSupervision {
        return $this->supervision;
    }

    public function getBlockName(): string {
        return 'supervision';
    }
}