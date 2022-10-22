<?php

namespace App\Markdown;

use EasySlugger\SluggerInterface;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\DocParser;
use League\CommonMark\EnvironmentInterface;
use Psr\Cache\CacheItemPoolInterface;

class TableOfContentsHelper {
    public function __construct(private SluggerInterface $slugger, private CacheItemPoolInterface $cache, private EnvironmentInterface $environment)
    {
    }

    public function getTableOfContents(string $markdown) {
        $hash = hash('sha512', $markdown);
        $key = sprintf('markdown.toc.%s', $hash);

        $item = $this->cache->getItem($key);

        if(!$item->isHit()) {
            $toc = $this->computeToc($markdown);

            $item->set(serialize($toc));
        }

        return unserialize($item->get());
    }

    private function computeToc(string $markdown): array {
        $parser = new DocParser($this->environment);
        $document = $parser->parse($markdown);
        $toc = $this->processDocument($document);

        $counter = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0
        ];

        $items = [ ];
        $lastLevel = 2;

        foreach($toc as $value) {
            $level = $value['level'];

            if($level < $lastLevel) {
                // reset counters
                for($i = $lastLevel; $i < count($counter); $i++) {
                    $counter[$i] = 0;
                }
            }

            $counter[$level]++;

            $currentLevel = '';
            for($i = 2; $i <= $level; $i++) {
                $currentLevel .= $counter[$i] . '.';
            }
            $currentLevel = substr($currentLevel, 0, -1);

            $items[] = [
                'level' => $currentLevel,
                'id' => $value['id'],
                'text' => $value['text']
            ];

            $lastLevel = $level;
        }

        return $items;
    }

    private function processDocument(Document $document): array {
        $toc = [ ];
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }

            $heading = $node->getStringContent();
            $slug = $this->slugger->slugify($heading);

            $level = min($node->getLevel() + 1, 6);

            $toc[] = [
                'id' => $slug,
                'level' => $level,
                'text' => $heading
            ];
        }

        return $toc;
    }
}