<?php

namespace App\Grouping;

use App\Converter\EnumStringConverter;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTypeAndGradeStrategy implements GroupingStrategyInterface, OptionsAwareGroupInterface {

    private $enumStringConverter;

    public function __construct(EnumStringConverter $enumStringConverter) {
        $this->enumStringConverter = $enumStringConverter;
    }

    /**
     * @param User $object
     * @return string[]|string|null
     */
    public function computeKey($object, array $options = [ ]) {
        if($object->getUserType()->equals(UserType::Student()) || $object->getUserType()->equals(UserType::Parent())) {
            if($object->getStudents()->count() === 0) {
                return null;
            }

            return $object->getStudents()->map(function(Student $student) use($options) {
                $grade = $student->getGrade($options['section']);

                return $grade?->getName();
            })->toArray();
        }

        return $this->enumStringConverter->convert($object->getUserType());
    }

    /**
     * @param string $keyA
     * @param string $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param string|null $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new StringGroup($key);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('section');
        $resolver->setAllowedTypes('section', Section::class);
    }
}