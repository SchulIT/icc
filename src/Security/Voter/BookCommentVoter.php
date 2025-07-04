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

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool {
        $attributes = [
            self::View,
            self::Edit,
            self::Remove
        ];

        return $attribute === self::New
            || (in_array($attribute, $attributes) && $subject instanceof BookComment);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::New => $this->canCreate($token),
            self::View => $this->canView($subject, $token),
            self::Edit, self::Remove => $this->canEditOrRemove($subject, $token),
            default => throw new LogicException('This code should not be reached.'),
        };
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    private function canView(BookComment $comment, TokenInterface $token): bool {
        /** @var User $user */
        $user = $token->getUser();

        if($user->isStudentOrParent()) {
            $userStudentIds = $user->getStudents()->map(fn(Student $student) => $student->getId())->toArray();

            $commentStudentIds = $comment->getStudents()->map(fn(Student $student) => $student->getId())->toArray();

            if(count(array_intersect($userStudentIds, $commentStudentIds)) > 0) {
                return $comment->canStudentAndParentsView();
            }

            return false;
        }

        return true;
    }

    public function canEditOrRemove(BookComment $comment, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }
}