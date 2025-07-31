<?php

namespace App\Book\Statistics;

use App\Entity\Tuition;
use App\Repository\TimetableLessonRepositoryInterface;
use Psr\Cache\InvalidArgumentException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class BookLessonCountGenerator {

    private const string KEY_PATTERN = 'book.tuition.%d.lesson_count';
    public const int LIFETIME_IN_SECONDS = 1200; // 20min

    public function __construct(private readonly CacheInterface $cache, private readonly TimetableLessonRepositoryInterface $lessonRepository, private readonly DateHelper $dateHelper) {}

    private function getCacheKey(Tuition $tuition): string {
        return sprintf(self::KEY_PATTERN, $tuition->getId());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function regenerate(Tuition $tuition): BookLessonCount {
        $this->cache->delete($this->getCacheKey($tuition));
        return $this->getCount($tuition);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getCount(Tuition $tuition): BookLessonCount {
        return $this->cache->get($this->getCacheKey($tuition), function(ItemInterface $item) use($tuition) {
            $item->expiresAfter(self::LIFETIME_IN_SECONDS);

            $holtCount = $this->lessonRepository->countHoldLessons([$tuition], null);
            $missingCount = $this->lessonRepository->countMissingByTuition(
                $tuition,
                $tuition->getSection()->getStart(),
                min($this->dateHelper->getToday(), $tuition->getSection()->getEnd())
            );

            return new BookLessonCount($holtCount, $missingCount);
        });
    }
}