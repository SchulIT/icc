<?php

namespace App\Chat;

use App\Entity\Chat;
use App\Entity\ChatTag;
use App\Entity\ChatUserTag;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\ChatTagRepositoryInterface;

class ChatTagHelper {

    public function __construct(private readonly ChatTagRepositoryInterface $repository) {

    }

    /**
     * @return ChatTag[]
     */
    public function getAll(UserType $userType): array {
        return $this->repository->findForUserType($userType);
    }

    /**
     * @param Chat $chat
     * @param User $user
     * @return ChatTag[]
     */
    public function getTagsForUser(Chat $chat, User $user): array {
        return $chat->getUserTags()->filter(fn(ChatUserTag $tag) => $tag->getUser()->getId() === $user->getId())->map(fn(ChatUserTag $tag) => $tag->getTag())->toArray();
    }

    /**
     * @param Chat[] $chats
     * @param ChatTag $tag
     * @param User $user
     * @return Chat[]
     */
    public function filterChats(array $chats, ChatTag $tag, User $user): array {
        $result = [ ];

        foreach($chats as $chat) {
            if(in_array($tag, $this->getTagsForUser($chat, $user))) {
                $result[] = $chat;
            }
        }

        return $result;
    }

    /**
     * @param Chat $chat
     * @param User $user
     * @param string[] $enabledTags UUID of enabled tags
     */
    public function synchronizeUserTags(Chat $chat, User $user, array $enabledTags): void {
        $toRemove = [ ]; // ChatUserTag items to remove
        $existingTags = [ ]; // UUID of existing tags

        foreach($chat->getUserTags() as $tag) {
            if($tag->getUser()->getId() !== $user->getId()) {
                continue;
            }

            if(!in_array($tag->getTag()->getUuid()->toString(), $enabledTags)) {
                $toRemove[] = $tag;
            } else {
                $existingTags[] = $tag->getTag()->getUuid()->toString();
            }
        }

        foreach($toRemove as $tag) {
            $chat->removeUserTag($tag);
        }

        foreach($this->getAll($user->getUserType()) as $tag) {
            if(in_array($tag->getUuid()->toString(), $enabledTags) && !in_array($tag->getUuid()->toString(), $existingTags)) {
                $userTag = (new ChatUserTag())
                    ->setChat($chat)
                    ->setTag($tag)
                    ->setUser($user);

                $chat->addUserTag($userTag);
            }
        }
    }
}