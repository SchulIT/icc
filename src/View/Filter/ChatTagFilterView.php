<?php

namespace App\View\Filter;

use App\Entity\ChatTag;

class ChatTagFilterView implements FilterViewInterface {

    /**
     * @param ChatTag[] $tags
     * @param ChatTag $currentTag
     */
    public function __construct(private readonly array $tags, private readonly ChatTag|null $currentTag) {

    }

    /**
     * @return ChatTag[]
     */
    public function getTags(): array {
        return $this->tags;
    }


    /**
     * @return ChatTag|null
     */
    public function getCurrentTag(): ChatTag|null {
        return $this->currentTag;
    }

    public function isEnabled(): bool {
        return count($this->tags) > 0;
    }
}