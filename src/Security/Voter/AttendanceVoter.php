<?php

namespace App\Security\Voter;

use App\Entity\Attendance;
use App\Entity\BookEvent;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AttendanceVoter extends Voter {

    public const Edit = 'edit';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::Edit && $subject instanceof Attendance;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::Edit:
                return $this->canEdit($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canEdit(Attendance $attendance, TokenInterface $token): bool {
        if($attendance->getEntry() !== null) {
            return $this->accessDecisionManager->decide($token, [ LessonEntryVoter::Edit ], $attendance->getEntry());
        }

        if($attendance->getEvent() !== null) {
            return $this->accessDecisionManager->decide($token, [ BookEventVoter::Edit ], $attendance->getEvent());
        }

        return false;
    }
}