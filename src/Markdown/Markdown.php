<?php

namespace App\Markdown;

use League\CommonMark\MarkdownConverterInterface;
use Psr\Cache\CacheItemPoolInterface;

class Markdown {
    public function __construct(private MarkdownConverterInterface $converter, private CacheItemPoolInterface $cache)
    {
    }

    public function convertToHtml(string $markdown): string {
        $hash = hash('sha512', $markdown);
        $key = sprintf('markdown.%s', $hash);

        $item = $this->cache->getItem($key);

        if(!$item->isHit()) {
            $html = $this->converter->convertToHtml($markdown);
            $item->set($html);
            $this->cache->save($item);
        }

        return $item->get();
    }
}