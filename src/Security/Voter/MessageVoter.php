<?php

namespace App\Security\Voter;

use App\Entity\Message;
use App\Entity\MessagePriority;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Message\MessageConfirmationHelper;
use App\Utils\EnumArrayUtils;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageVoter extends Voter {

    const New = 'new-message';
    const View = 'view';
    const Edit = 'edit';
    const Remove = 'remove';
    const Confirm = 'confirm';
    const Dismiss = 'dismiss';
    const Download = 'download';
    const Upload = 'upload';
    const Priority = 'message-priority';

    private $accessDecisionManager;
    private $confirmationHelper;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager, MessageConfirmationHelper $confirmationHelper) {
        $this->accessDecisionManager = $accessDecisionManager;
        $this->confirmationHelper = $confirmationHelper;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::View,
            static::Edit,
            static::Remove,
            static::Confirm,
            static::Dismiss,
            static::Download,
            static::Upload
        ];

        return in_array($attribute, [ static::New, static::Priority]) || (in_array($attribute, $attributes) && $subject instanceof Message);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::New:
                return $this->canCreate($token);

            case static::View:
                return $this->canView($subject, $token);

            case static::Edit:
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);

            case static::Confirm:
                return $this->canConfirm($subject, $token);

            case static::Dismiss:
                return $this->canDismiss($subject, $token);

            case static::Download:
                return $this->canDownload($subject, $token);

            case static::Upload:
                return $this->canUpload($subject, $token);

            case static::Priority:
                return $this->canSetPriority($token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canCreate(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_CREATOR']);
    }

    private function canView(Message $message, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        // Admins see all messages
        if($this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_ADMIN']) || $this->accessDecisionManager->decide($token, ['ROLE_KIOSK'])) {
            return true;
        }

        // Teachers can see all messages
        if($user->getUserType()->equals(UserType::Teacher())) {
            return true;
        }

        // You can see your own messages
        if($message->getCreatedBy()->getId() === $user->getId()) {
            return true;
        }

        if($this->isMemberOfTypeAndStudyGroup($token, $this->getUserTypes($message->getVisibilities()), $message->getStudyGroups()->toArray(), false) !== true) {
            return false;
        }

        if($user->getUserType()->equals(UserType::Student()) !== true && $user->getUserType()->equals(UserType::Parent()) !== true) {
            // all checks passed for non-student/-parent users
            return true;
        }

        /** @var Student[] $students */
        $students = $user->getStudents();
        $messageStudyGroupIds = $message->getStudyGroups()->map(function(StudyGroup $group) {
            return $group->getId();
        })->toArray();

        foreach($students as $student) {
            $studyGroupIds = $student->getStudyGroupMemberships()->map(function(StudyGroupMembership $membership) {
                return $membership->getStudyGroup()->getId();
            })->toArray();

            if(count(array_intersect($messageStudyGroupIds, $studyGroupIds)) > 1) {
                return true;
            }
        }

        return false;
    }

    private function canEdit(Message $message, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_CREATOR']) !== true) {
            return false;
        }

        if($this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_ADMIN'])) {
            // Admins can edit all messages
            return true;
        }

        // Creators can only edit their messages
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $message->getCreatedBy()->getId() === $user->getId();
    }

    private function canRemove(Message $message, TokenInterface $token) {
        return $this->canEdit($message, $token);
    }

    private function canConfirm(Message $message, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return false;
        }

        return $message->mustConfirm()
            && $this->isMemberOfTypeAndStudyGroup(
                $token,
                $this->getUserTypes($message->getConfirmationRequiredUserTypes()),
                $message->getConfirmationRequiredStudyGroups()->toArray(),
                true
            );
    }

    private function canDismiss(Message $message, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return false;
        }

        if($message->getPriority()->equals(MessagePriority::Emergency())) {
            return false;
        }

        if($message->mustConfirm() === false || $this->canConfirm($message, $token) === false) {
            return true;
        }

        // only allow dismissing message in case the user has confirmed the message!
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $this->confirmationHelper->isMessageConfirmed($message, $user);
    }

    /**
     * @param Collection<UserTypeEntity> $collection
     * @return UserType[]
     */
    private function getUserTypes(Collection $collection) {
        return array_map(function(UserTypeEntity $userTypeEntity) {
            return $userTypeEntity->getUserType();
        }, $collection->toArray());
    }

    /**
     * @param UserType[] $allowedUserTypes
     * @param UserType $userType
     * @param bool $strict
     * @return bool
     */
    private function checkUserType(array $allowedUserTypes, UserType $userType, bool $strict = true): bool {
        if(EnumArrayUtils::inArray($userType, $allowedUserTypes)) {
            return true;
        }

        if($strict === false && $userType->equals(UserType::Parent()) && EnumArrayUtils::inArray(UserType::Student(), $allowedUserTypes))  {
            return true;
        }

        return false;
    }

    /**
     * @param StudyGroup[] $studyGroups
     * @param Student[] $students
     * @return bool
     */
    private function isMemberOfStudyGroups(array $studyGroups, array $students): bool {
        foreach($students as $student) {
            foreach($studyGroups as $studyGroup) {
                /** @var StudyGroupMembership $membership */
                foreach($studyGroup->getMemberships() as $membership) {
                    if($membership->getStudent()->getId() === $student->getId()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param TokenInterface $token
     * @param UserType[] $userTypes
     * @param StudyGroup[] $studyGroups
     * @param bool $strict
     * @return bool
     */
    private function isMemberOfTypeAndStudyGroup(TokenInterface $token, array $userTypes, array $studyGroups, bool $strict = true): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if(EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]) && $this->isMemberOfStudyGroups($studyGroups, $user->getStudents()->toArray()) !== true) {
            return false;
        }

        return $this->checkUserType($userTypes, $user->getUserType(), $strict);
    }

    private function canDownload(Message $message, TokenInterface $token) {
        return $message->isDownloadsEnabled()
            && $this->isMemberOfTypeAndStudyGroup(
                $token,
                $this->getUserTypes($message->getDownloadEnabledUserTypes()),
                $message->getDownloadEnabledStudyGroups()->toArray(),
                true
            );
    }

    private function canUpload(Message $message, TokenInterface $token) {
        return $message->isUploadsEnabled()
            && $this->isMemberOfTypeAndStudyGroup(
                $token,
                $this->getUserTypes($message->getUploadEnabledUserTypes()),
                $message->getUploadEnabledStudyGroups()->toArray(),
                true
            );
    }

    private function canSetPriority(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_PRIORITY']);
    }
}