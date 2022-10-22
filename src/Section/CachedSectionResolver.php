<?php

namespace App\Section;

use App\Entity\Section;
use DateTime;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedSectionResolver implements SectionResolverInterface {

    public function __construct(private SectionResolver $sectionResolver, private CacheInterface $cache)
    {
    }

    public function getSectionForDate(DateTime $dateTime): ?Section {
        $key = sprintf('%s-%s', 'section', $dateTime->format('Y-m-d'));

        return $this->cache->get($key, function(ItemInterface $item) use($dateTime) {
            $item->expiresAfter(30);

            return $this->sectionResolver->getSectionForDate($dateTime);
        });
    }

    public function getCurrentSection(): ?Section {
        return $this->cache->get('current_section', function(ItemInterface $item) {
            $item->expiresAfter(30);

            return $this->sectionResolver->getCurrentSection();
        });
    }
}