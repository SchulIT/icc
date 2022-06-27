<?php

namespace App\Untis\Html\Substitution;

use DateTime;

class SubstitutionResult {

    private DateTime $dateTime;

    private array $substitutions = [ ];

    private array $freeLessons = [ ];

    private array $infotexts = [ ];

    private array $absences = [ ];

    public function __construct(DateTime $dateTime) {
        $this->dateTime = $dateTime;
    }

    public function addSubstitution(Substitution $substitution): SubstitutionResult {
        $this->substitutions[] = $substitution;
        return $this;
    }

    public function addFreeLesson(FreeLessons $freeLessons): SubstitutionResult {
        $this->freeLessons[] = $freeLessons;
        return $this;
    }

    public function addInfotext(Infotext $infotext): SubstitutionResult {
        $this->infotexts[] = $infotext;
        return $this;
    }

    public function addAbsence(Absence $absence): SubstitutionResult {
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
     * @return Substitution[]
     */
    public function getSubstitutions(): array {
        return $this->substitutions;
    }

    /**
     * @return FreeLessons[]
     */
    public function getFreeLessons(): array {
        return $this->freeLessons;
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
    public function getAbsences(): array {
        return $this->absences;
    }
}