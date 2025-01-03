<?php

namespace App\Markdown;

use App\Markdown\Node\Inline\Anchor;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\MarkdownParser;
use Psr\Cache\CacheItemPoolInterface;

class TableOfContentsHelper {
    public function __construct(private readonly CacheItemPoolInterface $cache, private readonly EnvironmentInterface $environment)
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
        $parser = new MarkdownParser($this->environment);
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

            if(!$node instanceof Heading || !$event->isEntering()) {
                continue;
            }

            $anchor = $node->firstChild();
            $text = $node->lastChild();

            if(!$anchor instanceof Anchor || !$text instanceof Text) {
                continue;
            }

            $heading = $text->getLiteral();

            $level = min($node->getLevel() + 1, 6);

            $toc[] = [
                'id' => $anchor->getId(),
                'level' => $level,
                'text' => $heading
            ];
        }

        return $toc;
    }
}