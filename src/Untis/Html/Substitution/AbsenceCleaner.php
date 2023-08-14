<?php

namespace App\Untis\Html\Substitution;

use App\Settings\TimetableSettings;
use App\Settings\UntisSettings;
use Doctrine\Common\Collections\ArrayCollection;

class AbsenceCleaner {
    public function __construct(private readonly UntisSettings $untisSettings, private readonly TimetableSettings $timetableSettings) { }

    /**
     * @param SubstitutionResult $result
     * @return Absence[]
     */
    public function getCleanedAbsences(SubstitutionResult $result): array {
        /** @var ArrayCollection<Absence> $collection */
        $collection = new ArrayCollection($result->getAbsences());

        /** @var ArrayCollection<Substitution> $substitutionCollection */
        $substitutionCollection = new ArrayCollection($result->getSubstitutions());

        /** @var Absence[] $resultingAbsences */
        $resultingAbsences = [ ];

        while($collection->isEmpty() === false) { // $collection acts as some sort of queue for this
            /** @var Absence $currentAbsence */
            $currentAbsence = $collection->first();
            /** @var Absence[] $absencesWithSameObjective */
            $absencesWithSameObjective = $collection->filter(fn(Absence $a) => $a->getObjective() === $currentAbsence->getObjective() && $a->getObjectiveType() === $currentAbsence->getObjectiveType())->toArray();

            $absentLessons = [ ];

            foreach($absencesWithSameObjective as $absence) {
                if($absence->getLessonStart() === null || $absence->getLessonEnd() === null) {
                    $absentLessons = array_merge($absentLessons, range(1, $this->timetableSettings->getMaxLessons()));
                } else {
                    $absentLessons = array_merge($absentLessons, range($absence->getLessonStart(), $absence->getLessonEnd()));
                }
            }

            $absentLessons = array_unique($absentLessons); // remove duplicates
            sort($absentLessons, SORT_NUMERIC); // sort ascending

            // Recreate absences
            if(count($absentLessons) === 0) {
                continue; // nothing to recreate here :)
            }

            if($this->untisSettings->isRemoveAbsenceOnEventEnabled() && !empty($this->untisSettings->getEventsType())) {
                /** @var Substitution[] $substitutions */
                $substitutions = $substitutionCollection->filter(
                    fn(Substitution $s) =>
                        $s->getType() === $this->untisSettings->getEventsType()
                        &&
                        (
                            ($currentAbsence->getObjectiveType() === AbsenceObjectiveType::Teacher && in_array($currentAbsence->getObjective(), $s->getTeachers()))
                            || ($currentAbsence->getObjectiveType() === AbsenceObjectiveType::StudyGroup && in_array($currentAbsence->getObjective(), $s->getGrades()))
                        )
                )->toArray();

                foreach($substitutions as $substitution) {
                    $toDelete = range($substitution->getLessonStart(), $substitution->getLessonEnd());
                    $absentLessons = array_diff($absentLessons, $toDelete);
                }
            }

            // just in case something goes wrong when calling array_diff above
            sort($absentLessons, SORT_NUMERIC);

            $lessonStartIdx = 0;
            for($idx = 1; $idx < count($absentLessons); $idx++) {
                if($absentLessons[$idx - 1] !== $absentLessons[$idx] - 1) { // we found a gap
                    $lessonEndIdx = $idx - 1;
                    $resultingAbsences[] = new Absence($currentAbsence->getObjectiveType(), $currentAbsence->getObjective(), $absentLessons[$lessonStartIdx], $absentLessons[$lessonEndIdx]);
                    $lessonStartIdx = $idx;

                    if($idx === count($absentLessons) - 1) { // edge case: last element is single item
                        $lessonEndIdx = $idx;
                        $resultingAbsences[] = new Absence($currentAbsence->getObjectiveType(), $currentAbsence->getObjective(), $absentLessons[$lessonStartIdx], $absentLessons[$lessonEndIdx]);
                    }
                } else if($idx === count($absentLessons) - 1) { // last absence
                    $lessonEndIdx = $idx;
                    $resultingAbsences[] = new Absence($currentAbsence->getObjectiveType(), $currentAbsence->getObjective(), $absentLessons[$lessonStartIdx], $absentLessons[$lessonEndIdx]);
                }
            }

            // Remove absences from original list as they are "processed"
            foreach($absencesWithSameObjective as $absence) {
                $collection->removeElement($absence);
            }
        }

        // Remove lessons if an absence is the whole day
        foreach($resultingAbsences as $absence) {
            if($absence->getLessonStart() === 1 && $absence->getLessonEnd() === $this->timetableSettings->getMaxLessons()) {
                $absence->setLessonStart(null);
                $absence->setLessonEnd(null);
            }
        }

        return $resultingAbsences;
    }
}