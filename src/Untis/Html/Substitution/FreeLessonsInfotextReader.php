<?php

namespace App\Untis\Html\Substitution;

class FreeLessonsInfotextReader implements InfotextReaderInterface {

    public const ItemSeparator = ',';

    public function canHandle(?string $identifier): bool {
        if($identifier === null) {
            return false;
        }

        return trim($identifier) === 'Unterrichtsfrei';
    }

    public function handle(SubstitutionResult $result, string $content): void {
        $content = $this->removeUnnecessaryText($content);

        foreach(explode(self::ItemSeparator, $content) as $item) {
            $result->addFreeLesson($this->handleItem(trim($item)));
        }
    }

    private function handleItem(string $content): FreeLessons {
        $lessons = explode('-', $content);

        if(count($lessons) === 2) {
            $lessonStart = intval($lessons[0]);
            $lessonEnd = intval($lessons[1]);
        } else {
            $lessonStart = intval($content);
            $lessonEnd = $lessonStart;
        }

        return new FreeLessons($lessonStart, $lessonEnd);
    }

    private function removeUnnecessaryText(string $content): string {
        $remove = ['Std.', 'Std', 'Stunden', 'Stunde'];
        return trim(str_replace($remove, '', $content));
    }
}