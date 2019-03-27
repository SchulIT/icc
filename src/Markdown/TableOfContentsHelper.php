<?php

namespace App\Markdown;

use EasySlugger\SluggerInterface;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Heading;

class TableOfContentsHelper {
    private $toc = [ ];

    private $slugger;

    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }

    public function getTableOfContents(Document $document) {
        $this->processDocument($document);

        $counter = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0
        ];

        $items = [ ];
        $lastLevel = 2;

        foreach($this->toc as $value) {
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

    private function processDocument(Document $document) {
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }

            $heading = $node->getStringContent();
            $slug = $this->slugger->slugify($heading);

            $level = min($node->getLevel() + 1, 6);

            $this->toc[] = [
                'id' => $slug,
                'level' => $level,
                'text' => $heading
            ];
        }
    }
}