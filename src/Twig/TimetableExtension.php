<?php

namespace App\Twig;

use App\Settings\TimetableSettings;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimetableExtension extends AbstractExtension {
    const HexColorRegExp = '/^\#?([0-9a-f]{6})$/s';

    private TranslatorInterface $translator;
    private TimetableSettings $timetableSettings;

    public function __construct(TranslatorInterface $translator, TimetableSettings $timetableSettings) {
        $this->translator = $translator;
        $this->timetableSettings = $timetableSettings;
    }

    public function getFilters(): array {
        return [
            new TwigFilter('weekday', [ $this, 'getWeekday' ]),
            new TwigFilter('before_lesson', [ $this, 'getBeforeLessonDescription'])
        ];
    }

    public function getWeekday(int $day, bool $short = false): string {
        $id = $short ? 'date.days_short.%d' : 'date.days.%d';

        return $this->translator->trans(
            sprintf($id, $day)
        );
    }

    public function getBeforeLessonDescription(int $lesson): string {
        $description = $this->timetableSettings->getDescriptionBeforeLesson($lesson);

        if(empty($description)) {
            return $this->translator->trans('dashboard.before_lesson', [ '%lesson%' => $lesson ]);
        }

        return $description;
    }
}