<?php

namespace App\Security\Voter;

use App\Entity\Document;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class DocumentVoter extends Voter {

    const New = 'new-document';
    const Edit = 'edit';
    const Remove = 'remove';
    const View = 'view';

    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::Edit,
            static::Remove,
            static::View
        ];

        return $attribute === static::New ||
            ($subject instanceof Document && in_array($attribute, $attributes));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::New:
                return $this->canCreateDocument();

            case static::Edit:
                return $this->canEditDocument($subject, $token);

            case static::Remove:
                return $this->canRemoveDocument();

            case static::View:
                return $this->canViewDocument($subject, $token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canCreateDocument() {
        return $this->security->isGranted('ROLE_DOCUMENTS_ADMIN');
    }

    private function canEditDocument(Document $document, TokenInterface $token) {
        if($this->security->isGranted('ROLE_DOCUMENTS_ADMIN')) {
            return true;
        }

        foreach($document->getAuthors() as $author) {
            if($author->getId() === $token->getUser()->getId()) {
                return true;
            }
        }

        return false;
    }

    private function canRemoveDocument() {
        return $this->security->isGranted('ROLE_DOCUMENTS_ADMIN');
    }

    private function canViewDocument(Document $document, TokenInterface $token) {
        /** @var User $user */
        $user = $token->getUser();

        foreach($document->getVisibilities() as $visibility) {
            if($visibility->getUserType()->equals($user->getUserType())) {
                // user type is in the visibility scope
                if(!$visibility->getUserType()->equals(UserType::Student()) && !$visibility->getUserType()->equals(UserType::Parent())) {
                    // no additional checks if usertype isn't student or teacher
                    return true;
                }

                // now also check study group membership
                $studentIds = $user->getStudents()->map(function(Student $student) {
                    return $student->getId();
                })->toArray();

                foreach($document->getStudyGroups() as $documentStudyGroup) {
                    foreach($documentStudyGroup->getMemberships() as $membership) {
                        $studentId = $membership->getStudent()->getId();

                        if(in_array($studentId, $studentIds)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}