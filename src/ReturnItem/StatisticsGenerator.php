<?php

namespace App\ReturnItem;

use App\Entity\ReturnItemType;
use App\Repository\ReturnItemRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Utils\ArrayUtils;
use DateTime;
use Psr\Cache\CacheItemInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Cache\CacheInterface;

readonly class StatisticsGenerator {
    public function __construct(private CacheInterface $cache,
                                private ReturnItemRepositoryInterface $repository,
                                private SectionResolverInterface $sectionResolver,
                                private DateHelper $dateHelper) {

    }

    public function getStatistics(DateTime $start, DateTime $end, ReturnItemType|null $type): Statistics {
        $key = sprintf(
            'return_items.statistics.s_%s.e_%s.t_%s',
            $start->format('Y-m-d'),
            $end->format('Y-m-d'),
            $type?->getId()
        );

        return $this->cache->get($key, function(CacheItemInterface $cacheItem) use ($start, $end, $type) {
            $cacheItem->expiresAfter(3600);

            $items = $this->repository->findForRange($start, $end, $type);

            $section = $this->sectionResolver->getCurrentSection();

            /** @var array<int, Row> $rows */
            $rows = [ ];
            foreach ($items as $item) {
                $studentId = $item->getStudent()->getId();

                if(!array_key_exists($studentId, $rows)) {
                    $rows[$studentId] = new Row($studentId, 0, $item->getStudent()->getGrade($section)?->getName());
                }

                $rows[$studentId]->itemsCount++;
            }

            usort($rows, function(Row $a, Row $b) {
                return $a->itemsCount - $b->itemsCount;
            });

            return new Statistics(
                $start,
                $end,
                count($items),
                ArrayUtils::createArrayWithKeys(
                    $rows,
                    fn(Row $row): int => $row->studentId
                ),
                $this->dateHelper->getNow()
            );
        });
    }
}