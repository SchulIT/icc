<?php

namespace App\Book\Statistics;

use App\Entity\Teacher;
use App\Repository\TimetableLessonRepositoryInterface;
use DateTime;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class MissingEntriesCalculator {

    private const string KEY_PATTERN = 'book.missing_entries.%s.%s-%s.lesson_count';
    public const int LIFETIME_IN_SECONDS = 600; // 10min

    public function __construct(
        private TimetableLessonRepositoryInterface $lessonRepository,
        private CacheInterface $cache
    ) {

    }

    public function countMissingByTeacher(Teacher $teacher, DateTime $start, DateTime $end): int {
        $key = sprintf(self::KEY_PATTERN, $teacher->getId(), $start->format('Ymd'), $end->format('Ymd'));

        return $this->cache->get($key, function(ItemInterface $item) use ($teacher, $start, $end): int {
            $item->expiresAfter(self::LIFETIME_IN_SECONDS);
            return $this->lessonRepository->countMissingByTeacher($teacher, $start, $end);
        });
    }
}