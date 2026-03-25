<?php

namespace App\Autocomplete\User;

use App\Autocomplete\Item;
use App\Converter\StudentStringConverter;
use App\Converter\UserStringConverter;
use App\Entity\Student;
use App\Entity\User;
use App\Section\SectionResolverInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class UserTransformator {
    public function __construct(
        private TranslatorInterface $translator,
        private StudentStringConverter $studentStringConverter,
        private UserStringConverter $userStringConverter,
        private SectionResolverInterface $sectionResolver
    ) {

    }

    public function transform(User $user): Item {
        $label = $this->userStringConverter->convert($user);

        $sublabel = '';

        if($user->isStudent() && ($section = $this->sectionResolver->getCurrentSection()) !== null) {
            $sublabel = $user->getStudents()->first()?->getGrade($section)?->getName();
        } else if($user->isParent()) {
            $students = $user->getStudents()->map(fn(Student $student) => $this->studentStringConverter->convert($student, true))->toArray();
            $sublabel = $this->translator->trans('label.parent_of', ['%students%' => implode('; ', $students)]);
        }

        return new Item(
            $user->getIdpId()->toString(),
            $label,
            $sublabel,
            $user->getUserType()->trans($this->translator),
        );
    }
}