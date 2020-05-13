<?php

namespace App\Dashboard;

abstract class AbstractViewItem {

    private $isVisible = true;

    public function hide(): void {
        $this->isVisible = false;
    }

    public function isVisible(): bool {
        return $this->isVisible;
    }

    public abstract function getBlockName(): string;
}