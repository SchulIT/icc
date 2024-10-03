<?php

namespace App\View\Filter;

use App\Chat\ChatTagHelper;
use App\Entity\User;

class ChatTagFilter {
    public function __construct(private readonly ChatTagHelper $chatTagHelper) {

    }

    public function handle(?string $tagUuid, User $user): ChatTagFilterView {
        $tags = $this->chatTagHelper->getAll($user->getUserType());
        $selectedTag = null;

        foreach($tags as $tag) {
            if($tagUuid === (string)$tag->getUuid()) {
                $selectedTag = $tag;
            }
        }

        return new ChatTagFilterView($tags, $selectedTag);
    }
}