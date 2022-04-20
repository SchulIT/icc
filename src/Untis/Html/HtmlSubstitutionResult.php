<?php

namespace App\Untis\Html;

use DateTime;

class HtmlSubstitutionResult {

    private DateTime $dateTime;

    private array $substitutions = [ ];

    private array $freeLessons = [ ];

    private array $infotexts = [ ];

    private array $absences = [ ];

    public function __construct(DateTime $dateTime) {
        $this->dateTime = $dateTime;
    }

    public function addSubstitution(HtmlSubstitution $substitution): HtmlSubstitutionResult {
        $this->substitutions[] = $substitution;
        return $this;
    }

    public function addFreeLesson(HtmlFreeLessons $freeLessons): HtmlSubstitutionResult {
        $this->freeLessons[] = $freeLessons;
        return $this;
    }

    public function addInfotext(HtmlInfotext $infotext): HtmlSubstitutionResult {
        $this->infotexts[] = $infotext;
        return $this;
    }

    public function addAbsence(HtmlAbsence $absence): HtmlSubstitutionResult {
        $this->absences[] = $absence;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateTime(): DateTime {
        return $this->dateTime;
    }

    /**
     * @return HtmlSubstitution[]
     */
    public function getSubstitutions(): array {
        return $this->substitutions;
    }

    /**
     * @return HtmlFreeLessons[]
     */
    public function getFreeLessons(): array {
        return $this->freeLessons;
    }

    /**
     * @return HtmlInfotext[]
     */
    public function getInfotexts(): array {
        return $this->infotexts;
    }

    /**
     * @return HtmlAbsence[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }
}