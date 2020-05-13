<?php

namespace App\Dashboard;

use App\Entity\Absence;
use App\Entity\Infotext;
use App\Entity\Message;

class DashboardView {

    /** @var Message[] */
    private $messages = [ ];

    /** @var Infotext[] */
    private $infotexts = [ ];

    /** @var Absence[] */
    private $absentTeachers = [ ];

    /** @var Absence[] */
    private $absentStudyGroups = [ ];

    private $items = [ ];

    private $beforeItems = [ ];

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    public function getLessons(): array {
        $lessons = array_merge(array_keys($this->items), array_keys($this->beforeItems));
        sort($lessons, SORT_NUMERIC);

        return array_unique($lessons);
    }

    /**
     * @return AbstractViewItem[]
     */
    public function getItems(int $lesson): array {
        return $this->items[$lesson] ?? [ ];
    }

    /**
     * @return AbstractViewItem[]
     */
    public function getItemsBefore(int $lesson): array {
        return $this->beforeItems[$lesson] ?? [ ];
    }

    /**
     * @return Infotext[]
     */
    public function getInfotexts(): array {
        return $this->infotexts;
    }

    /**
     * @return Absence[]
     */
    public function getAbsentTeachers(): array {
        return $this->absentTeachers;
    }

    /**
     * @return Absence[]
     */
    public function getAbsentStudyGroups(): array {
        return $this->absentStudyGroups;
    }

    public function addItem(int $lesson, AbstractViewItem $item): void {
        if(!isset($this->items[$lesson])) {
            $this->items[$lesson] = [ ];
        }

        $this->items[$lesson][] = $item;
    }

    public function addItemBefore(int $lesson, AbstractViewItem $item): void {
        if(!isset($this->beforeItems[$lesson])) {
            $this->beforeItems[$lesson] = [ ];
        }

        $this->beforeItems[$lesson][] = $item;
    }

    public function addMessage(Message $message): void {
        $this->messages[] = $message;
    }

    public function addInfotext(Infotext $infotext): void {
        $this->infotexts[] = $infotext;
    }

    public function addAbsence(Absence $absence): void {
        if($absence->getStudyGroup() !== null) {
            $this->absentStudyGroups[] = $absence;
        } else if($absence->getTeacher() !== null) {
            $this->absentTeachers[] = $absence;
        }
    }

    public function isEmpty(): bool {
        return count($this->messages) === 0
            && count($this->infotexts) === 0
            && count($this->items) === 0
            && count($this->beforeItems) === 0
            && count($this->absentStudyGroups) === 0
            && count($this->absentTeachers) === 0;
    }
}