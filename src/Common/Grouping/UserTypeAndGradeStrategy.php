<?php

namespace App\Common\Grouping;

use App\Framework\Converter\EnumStringConverter;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\OptionsAwareGroupInterface;
use App\Framework\Grouping\StringGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTypeAndGradeStrategy implements GroupingStrategyInterface, OptionsAwareGroupInterface {

    public function __construct(private EnumStringConverter $enumStringConverter)
    {
    }

    /**
     * @param User $object
     * @return string[]|string|null
     */
    public function computeKey($object, array $options = [ ]) {
        if($object->isStudentOrParent()) {
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
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param string|null $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new StringGroup($key);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('section');
        $resolver->setAllowedTypes('section', Section::class);
    }
}