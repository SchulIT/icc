<?php

namespace App\Common\Autocomplete\User;

use App\Framework\Autocomplete\Item;
use App\Common\Converter\StudentStringConverter;
use App\Common\Converter\UserStringConverter;
use App\Common\Entity\Student;
use App\Common\Entity\User;
use App\Common\Section\SectionResolverInterface;
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