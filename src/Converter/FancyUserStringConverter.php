<?php

namespace App\Converter;

use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use Symfony\Contracts\Translation\TranslatorInterface;

class FancyUserStringConverter {

    public function __construct(private readonly TranslatorInterface $translator,
                                private readonly StudentStringConverter $studentStringConverter) {

    }

    public function convert(User $user): string {
        $label = $this->translator->trans('label.empty_name');
        if(!empty($user->getFirstname()) && !empty($user->getLastname())) {
            $label = sprintf('%s, %s', $user->getLastname(), $user->getFirstname());
        }

        $label .= ' ';

        if($user->isStudent()) {
            $label = $this->studentStringConverter->convert($user->getStudents()->first(), true) .' ';
        } if($user->isParent()) {
            $students = $user->getStudents()->map(fn(Student $student) => $this->studentStringConverter->convert($student, true))->toArray();

            $label .= sprintf('(%s)', $this->translator->trans('label.parent_of', ['%students%' => implode('; ', $students)]));
        } else {
            $label .= sprintf('(%s)', $this->convertUserTypeToString($user->getUserType()));
        }

        return $label;
    }

    private function convertUserTypeToString(UserType $userType): string {
        return $this->translator->trans(sprintf('user_type.%s', $userType->value));
    }
}