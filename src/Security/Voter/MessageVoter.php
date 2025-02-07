<?php

namespace App\Security\Voter;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Message\MessageConfirmationHelper;
use App\Utils\ArrayUtils;
use Doctrine\Common\Collections\Collection;
use LogicException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageVoter extends Voter {

    public const New = 'new-message';
    public const View = 'view';
    public const Edit = 'edit';
    public const Remove = 'remove';
    public const Confirm = 'confirm';
    public const Dismiss = 'dismiss';
    public const Download = 'download';
    public const Upload = 'upload';
    public const Priority = 'message-priority';
    public const Poll = 'poll';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager, private MessageConfirmationHelper $confirmationHelper, private DateHelper $dateHelper, private readonly FeatureManager $featureManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::View,
            self::Edit,
            self::Remove,
            self::Confirm,
            self::Dismiss,
            self::Download,
            self::Upload,
            self::Poll
        ];

        return in_array($attribute, [ self::New, self::Priority]) || (in_array($attribute, $attributes) && $subject instanceof Message);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if($this->featureManager->isFeatureEnabled(Feature::Messages) !== true) {
            return false;
        }

        return match ($attribute) {
            self::New => $this->canCreate($token),
            self::View => $this->canView($subject, $token),
            self::Edit => $this->canEdit($subject, $token),
            self::Remove => $this->canRemove($subject, $token),
            self::Confirm => $this->canConfirm($subject, $token),
            self::Dismiss => $this->canDismiss($subject, $token),
            self::Download => $this->canDownload($subject, $token),
            self::Upload => $this->canUpload($subject, $token),
            self::Priority => $this->canSetPriority($token),
            self::Poll => $this->canVote($subject, $token),
            default => throw new LogicException('This code should not be reached.'),
        };
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_CREATOR']);
    }

    private function canView(Message $message, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        // Admins see all messages
        if($this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_ADMIN']) || $this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_VIEWER '])) {
            return true;
        }

        // Teachers can see all messages
        if($user->isTeacher()) {
            return true;
        }

        // You can see your own messages
        if($message->getCreatedBy() !== null && $message->getCreatedBy()->getId() === $user->getId()) {
            return true;
        }

        if($this->isMemberOfTypeAndStudyGroup($token, $this->getUserTypes($message->getVisibilities()), $message->getStudyGroups()->toArray(), false) === true) {
            return true;
        }

        if($user->isStudentOrParent() === false) {
            // all checks passed for non-student/-parent users
            return true;
        }

        return false;
    }

    private function canEdit(Message $message, TokenInterface $token): bool {
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

    private function canRemove(Message $message, TokenInterface $token): bool {
        return $this->canEdit($message, $token);
    }

    private function canConfirm(Message $message, TokenInterface $token): bool {
        return $message->mustConfirm()
            && $this->isMemberOfTypeAndStudyGroup(
                $token,
                $this->getUserTypes($message->getConfirmationRequiredUserTypes()),
                $message->getConfirmationRequiredStudyGroups()->toArray(),
                true
            );
    }

    private function canVote(Message $message, TokenInterface $token): bool {
        return $message->isPollEnabled()
            && $this->isMemberOfTypeAndStudyGroup(
                $token,
                $this->getUserTypes($message->getPollUserTypes()),
                $message->getPollStudyGroups()->toArray(),
                true
            );
    }

    private function canDismiss(Message $message, TokenInterface $token): bool {
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
    private function getUserTypes(Collection $collection): array {
        return array_map(fn(UserTypeEntity $userTypeEntity) => $userTypeEntity->getUserType(), $collection->toArray());
    }

    /**
     * @param UserType[] $allowedUserTypes
     */
    private function checkUserType(array $allowedUserTypes, UserType $userType, bool $strict = true): bool {
        if(ArrayUtils::inArray($userType, $allowedUserTypes)) {
            return true;
        }

        if($strict === false && $userType === UserType::Parent && ArrayUtils::inArray(UserType::Student, $allowedUserTypes))  {
            return true;
        }

        return false;
    }

    /**
     * @param StudyGroup[] $studyGroups
     * @param Student[] $students
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
     * @param UserType[] $userTypes
     * @param StudyGroup[] $studyGroups
     */
    private function isMemberOfTypeAndStudyGroup(TokenInterface $token, array $userTypes, array $studyGroups, bool $strict = true): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->isStudentOrParent() && $this->isMemberOfStudyGroups($studyGroups, $user->getStudents()->toArray()) !== true) {
            return false;
        }

        return $this->checkUserType($userTypes, $user->getUserType(), $strict);
    }

    private function canDownload(Message $message, TokenInterface $token): bool {
        return $message->isDownloadsEnabled()
            && $this->isMemberOfTypeAndStudyGroup(
                $token,
                $this->getUserTypes($message->getDownloadEnabledUserTypes()),
                $message->getDownloadEnabledStudyGroups()->toArray(),
                true
            );
    }

    private function canUpload(Message $message, TokenInterface $token): bool {
        return $message->isUploadsEnabled()
            && $this->dateHelper->getToday() <= $message->getExpireDate()
            && $this->isMemberOfTypeAndStudyGroup(
                $token,
                $this->getUserTypes($message->getUploadEnabledUserTypes()),
                $message->getUploadEnabledStudyGroups()->toArray(),
                true
            );
    }

    private function canSetPriority(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_PRIORITY']);
    }
}