<?php

namespace App\Markdown;

use Emojione\Client;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Markdown {
    private $converter;
    private $emojiConverter;
    private $cache;

    public function __construct(CommonMarkConverter $converter, Client $client, AdapterInterface $cache) {
        $this->converter = $converter;
        $this->emojiConverter = $client;
        $this->cache = $cache;
    }

    public function convertToHtml(string $markdown): string {
        $hash = hash('sha512', $markdown);
        $key = sprintf('markdown.%s', $hash);

        $item = $this->cache->getItem($key);

        if(!$item->isHit()) {
            $markdown = $this->emojiConverter->toShort($markdown);
            $html = $this->converter->convertToHtml($markdown);

            $html = $this->emojiConverter->toImage($html);
            $item->set($html);
            $this->cache->save($item);
        }

        return $item->get();
    }
}