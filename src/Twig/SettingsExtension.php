<?php

namespace App\Twig;

use App\Entity\Grade;
use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension {

    private $timetableSettings;

    public function __construct(TimetableSettings $timetableSettings) {
        $this->timetableSettings = $timetableSettings;
    }

    public function getFunctions() {
        return [
            new TwigFunction('show_coursename', [ $this, 'showCourseName'])
        ];
    }

    /**
     * @param iterable|Grade[] $grades
     * @return bool
     */
    public function showCourseName(iterable $grades): bool {
        $grades = ArrayUtils::iterableToArray($grades);
        $gradesWithCourseNames = $this->timetableSettings->getGradeIdsWithCourseNames();

        foreach($grades as $grade) {
            if(in_array($grade->getId(), $gradesWithCourseNames)) {
                return true;
            }
        }

        return false;
    }
}