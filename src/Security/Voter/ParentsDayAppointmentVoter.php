<?php

namespace App\Security\Voter;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\User;
use App\Repository\ParentsDayRepositoryInterface;
use LogicException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ParentsDayAppointmentVoter extends Voter {

    public const VIEW = 'parentsdayappointments';
    public const DETAILS = 'details';
    public const CREATE = 'create-parentsdayappointment';
    public const EDIT = 'edit';
    public const CANCEL = 'cancel';
    public const REMOVE = 'remove';

    public const BOOK_ANY = 'book-parentsdayappointment';
    public const BOOK = 'book';

    public const UNBOOK = 'unbook';

    public function __construct(private readonly DateHelper $dateHelper, private readonly ParentsDayRepositoryInterface $parentsDayRepository) {

    }


    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute == self::VIEW || $attribute === self::CREATE) {
            return true;
        }

        if($subject instanceof ParentsDay && $attribute === self::BOOK_ANY) {
            return true;
        }

        return $subject instanceof ParentsDayAppointment
            && in_array($attribute, [ self::EDIT, self::CANCEL, self::CREATE, self::REMOVE, self::DETAILS, self::BOOK, self::UNBOOK ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::VIEW:
                return $this->canView($token);

            case self::EDIT:
            case self::REMOVE:
                return $this->canEdit($subject, $token);

            case self::CANCEL:
                return $this->canCancel($subject, $token);

            case self::DETAILS:
                return $this->canViewDetails($subject, $token);

            case self::CREATE:
                return $this->canCreate($token);

            case self::BOOK:
                return $this->canBook($subject, $token);

            case self::UNBOOK:
                return $this->canUnbook($subject, $token);

            case self::BOOK_ANY:
                return $this->canBookAny($subject, $token);
        }

        throw new LogicException('This should not be executed.');
    }

    private function getUser(TokenInterface $token): ?User {
        $user = $token->getUser();

        if($user instanceof User) {
            return $user;
        }

        return null;
    }

    private function canCancel(ParentsDayAppointment $appointment, TokenInterface $token): bool {
        if($this->canView($token) !== true) {
            return false;
        }

        // Appointments can only be cancelled after booking window
        if($appointment->getParentsDay()->getBookingAllowedUntil() >= $this->dateHelper->getToday()) {
            return false;
        }

        if($appointment->isCancelled()) {
            return false;
        }

        $user = $this->getUser($token);

        if($user->isStudentOrParent()) {
            foreach($user->getStudents() as $student) {
                foreach($appointment->getStudents() as $appointmentStudent) {
                    if($appointmentStudent->getId() === $student->getId()) {
                        return true;
                    }
                }
            }
        } else if($user->isTeacher()) {
            foreach($appointment->getTeachers() as $teacher) {
                if($teacher->getId() === $user->getTeacher()->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    private function canView(TokenInterface $token): bool {
        $user = $this->getUser($token);

        if($user === null) {
            return false;
        }

        if($user->isTeacher()) {
            $parentsDays = $this->parentsDayRepository->findAll();
            return count($parentsDays) > 0;
        }

        $upcoming = $this->parentsDayRepository->findUpcoming($this->dateHelper->getToday());

        foreach($upcoming as $parentsDay) {
            if($parentsDay->getBookingAllowedFrom() <= $this->dateHelper->getToday()) {
                return $user->isStudentOrParent();
            }
        }

        return false;
    }

    private function canEdit(ParentsDayAppointment $appointment, TokenInterface $token): bool {
        if(!$this->canView($token)) {
            return false;
        }

        if($appointment->isCancelled()) {
            return false;
        }

        $user = $this->getUser($token);

        if(!$user->isTeacher()) {
            return false;
        }

        foreach($appointment->getTeachers() as $appointmentTeacher) {
            if($appointmentTeacher->getId() === $user->getTeacher()->getId()) {
                return true;
            }
        }

        return false;
    }

    private function canViewDetails(ParentsDayAppointment $appointment, TokenInterface $token): bool {
        if(!$this->canView($token)) {
            return false;
        }

        $user = $this->getUser($token);

        if($user->isStudentOrParent()) {
            foreach($user->getStudents() as $student) {
                foreach($appointment->getStudents() as $appointmentStudent) {
                    if($student->getId() === $appointmentStudent->getId()) {
                        return true;
                    }
                }
            }
        }

        return $user->isTeacher();
    }

    private function canCreate(TokenInterface $token): bool {
        if(!$this->canView($token)) {
            return false;
        }

        $user = $this->getUser($token);

        return $user->isTeacher();
    }

    private function canBookAny(ParentsDay $parentsDay, TokenInterface $token): bool {
        if(!$this->canView($token)) {
            return false;
        }

        $user = $this->getUser($token);

        if($parentsDay->getBookingAllowedFrom() > $this->dateHelper->getToday()) {
            return false;
        }

        if($parentsDay->getBookingAllowedUntil() < $this->dateHelper->getToday()) {
            return false;
        }

        if($user->isParent()) {
            return true;
        }

        if($user->isStudent()) {
            /** @var Student $student */
            $student = $user->getStudents()->first();

            return $student->isFullAged($this->dateHelper->getToday());
        }

        return false;
    }

    private function canBook(ParentsDayAppointment $appointment, TokenInterface $token): bool {
        if($appointment->isBlocked() || $appointment->isCancelled() || $appointment->getStudents()->count() > 0) {
            return false;
        }

        return $this->canBookAny($appointment->getParentsDay(), $token);
    }

    private function canUnbook(ParentsDayAppointment $appointment, TokenInterface $token): bool {
        if($this->canBookAny($appointment->getParentsDay(), $token) !== true) {
            return false;
        }

        if($appointment->isCancelled()) {
            return false;
        }

        if($appointment->getTeachers()->count() > 1) {
            return false;
        }

        $user = $this->getUser($token);

        foreach($appointment->getStudents() as $appointmentStudent) {
            foreach($user->getStudents() as $student) {
                if($appointmentStudent->getId() === $student->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}