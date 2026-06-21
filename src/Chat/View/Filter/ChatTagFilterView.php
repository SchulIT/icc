<?php

namespace App\Chat\View\Filter;

use App\Chat\Entity\ChatTag;
use App\Framework\View\Filter\FilterViewInterface;

readonly class ChatTagFilterView implements FilterViewInterface {

    /**
     * @param ChatTag[] $tags
     * @param ChatTag|null $currentTag
     */
    public function __construct(private array $tags, private ChatTag|null $currentTag) {

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
