<?php

namespace App\Untis\Html;

class FreeLessonsInfotextReader implements InfotextReaderInterface {

    public const ItemSeparator = ',';

    public function canHandle(?string $identifier): bool {
        return trim($identifier) === 'Unterrichtsfrei';
    }

    public function handle(HtmlSubstitutionResult $result, string $content): void {
        $content = $this->removeUnnecessaryText($content);

        foreach(explode(static::ItemSeparator, $content) as $item) {
            $result->addFreeLesson($this->handleItem(trim($item)));
        }
    }

    private function handleItem(string $content): HtmlFreeLessons {
        $lessons = explode('-', $content);

        if(count($lessons) === 2) {
            $lessonStart = intval($lessons[0]);
            $lessonEnd = intval($lessons[1]);
        } else {
            $lessonStart = intval($content);
            $lessonEnd = $lessonStart;
        }

        return new HtmlFreeLessons($lessonStart, $lessonEnd);
    }

    private function removeUnnecessaryText(string $content): string {
        $remove = ['Std.', 'Std', 'Stunden', 'Stunde'];
        return trim(str_replace($remove, '', $content));
    }
}