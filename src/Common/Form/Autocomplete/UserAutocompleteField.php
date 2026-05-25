<?php

namespace App\Common\Form\Autocomplete;

use App\Common\Converter\StudentStringConverter;
use App\Common\Converter\UserStringConverter;
use App\Common\Entity\Student;
use App\Common\Entity\User;
use App\Common\Section\SectionResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class UserAutocompleteField extends AbstractType {

    public function __construct(
        private readonly SectionResolverInterface $sectionResolver,
        private readonly StudentStringConverter $studentStringConverter,
        private readonly UserStringConverter $userStringConverter,
        private readonly TranslatorInterface $translator
    ) {

    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'class' => User::class,
            'placeholder' => 'label.select.user',
            'security' => 'ROLE_SUPER_ADMIN',
            'searchable_fields' => [
                'username',
                'email',
                'firstname',
                'lastname',
                'students.email',
                'students.firstname',
                'students.lastname',
            ],
            'choice_label' => fn(User $user): string => $this->userStringConverter->convert($user),
            'additional_attributes' => function(User $user) {
                $sublabel = '';
                $extra = $user->getUserType()->trans($this->translator);

                if($user->isStudent() && ($section = $this->sectionResolver->getCurrentSection()) !== null) {
                    $sublabel = $user->getStudents()->first()?->getGrade($section)?->getName();
                } else if($user->isParent()) {
                    $students = $user->getStudents()->map(fn(Student $student) => $this->studentStringConverter->convert($student, true))->toArray();
                    $sublabel = $this->translator->trans('label.parent_of', ['%students%' => implode('; ', $students)]);
                }

                return [
                    'sublabel' => $sublabel,
                    'extra' => $extra
                ];
            }
        ]);
    }

    public function getParent(): string {
        return BaseEntityAutocompleteType::class;
    }
}