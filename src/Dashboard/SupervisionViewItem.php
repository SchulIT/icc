<?php

namespace App\Dashboard;

use App\Entity\TimetableSupervision;

class SupervisionViewItem extends AbstractViewItem {

    private $supervision;

    public function __construct(TimetableSupervision $supervision) {
        $this->supervision = $supervision;
    }

    /**
     * @return TimetableSupervision
     */
    public function getSupervision(): TimetableSupervision {
        return $this->supervision;
    }

    public function getBlockName(): string {
        return 'supervision';
    }
}