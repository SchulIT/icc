<?php

namespace App\Twig;

use App\Settings\TimetableSettings;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimetableExtension extends AbstractExtension {
    const HexColorRegExp = '/^\#?([0-9a-f]{6})$/s';

    private $translator;
    private $timetableSettings;

    public function __construct(TranslatorInterface $translator, TimetableSettings $timetableSettings) {
        $this->translator = $translator;
        $this->timetableSettings = $timetableSettings;
    }

    public function getFilters() {
        return [
            new TwigFilter('weekday', [ $this, 'getWeekday' ]),
            new TwigFilter('foreground', [ $this, 'getForegroundColor' ]),
            new TwigFilter('before_lesson', [ $this, 'getBeforeLessonDescription'])
        ];
    }

    public function getWeekday(int $day, bool $short = false) {
        $id = $short ? 'date.days_short.%d' : 'date.days.%d';

        return $this->translator->trans(
            sprintf($id, $day)
        );
    }

    public function getForegroundColor(string $backgroudColor) {
        if(!preg_match(static::HexColorRegExp, $backgroudColor)) {
            throw new \InvalidArgumentException(sprintf('Invalid HTML hex color "%s"', $backgroudColor));
        }

        if(substr($backgroudColor, 0, 1) === '#') {
            list($r, $g, $b) = sscanf($backgroudColor, "#%02x%02x%02x");
        } else {
            list($r, $g, $b) = sscanf($backgroudColor, "%02x%02x%02x");
        }

        $intensity = $r*0.299 + $g*0.587 + $b*0.114;

        if($intensity > 186) {
            return 'black';
        }

        return 'white';
    }

    public function getBeforeLessonDescription(int $lesson): string {
        $description = $this->timetableSettings->getDescriptionBeforeLesson($lesson);

        if(empty($description)) {
            return $this->translator->trans('dashboard.before_lesson', [ '%lesson%' => $lesson ]);
        }

        return $description;
    }
}