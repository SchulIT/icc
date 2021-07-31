<?php

namespace App\Security\Voter;

use App\Entity\BookComment;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BookCommentVoter extends Voter {

    public const New = 'new-entry';
    public const View = 'view';
    public const Edit = 'edit';
    public const Remove = 'remove';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject) {
        $attributes = [
            static::View,
            static::Edit,
            static::Remove
        ];

        return $attribute === static::New
            || (in_array($attribute, $attributes) && $subject instanceof BookComment);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::New:
                return $this->canCreate($token);

            case static::View:
                return $this->canView($subject, $token);

            case static::Edit:
            case static::Remove:
                return $this->canEditOrRemove($subject, $token);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    private function canView(BookComment $comment, TokenInterface $token): bool {
        /** @var User $user */
        $user = $token->getUser();

        if(EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ])) {
            $userStudentIds = $user->getStudents()->map(function(Student $student) {
                return $student->getId();
            })->toArray();

            $commentStudentIds = $comment->getStudents()->map(function(Student $student) {
                return $student->getId();
            })->toArray();

            if(count(array_intersect($userStudentIds, $commentStudentIds)) > 0) {
                return true;
            }

            return false;
        }

        return true;
    }

    public function canEditOrRemove(BookComment $comment, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }
}