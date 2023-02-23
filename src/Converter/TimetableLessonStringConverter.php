<?php

namespace App\Converter;

use App\Entity\TimetableLesson;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimetableLessonStringConverter {

    public function __construct(private readonly TranslatorInterface $translator) { }

    public function convert(TimetableLesson $lesson): string {
        $day = $this->translator->trans(sprintf('date.days_short.%d', $lesson->getDate()->format('w')));
        $date = $lesson->getDate()->format($this->translator->trans('date.format_short'));
        $lessons = $this->translator->trans('label.substitution_lessons', [
            '%start%' => $lesson->getLessonStart(),
            '%end%' => $lesson->getLessonEnd(),
            '%count%' => $lesson->getLessonEnd() - $lesson->getLessonStart()
        ]);

        return sprintf('%s - %s - %s - %s - %s', $day, $date, $lessons, $lesson->getTuition()->getStudyGroup(), $lesson->getTuition()->getSubject());
    }
}