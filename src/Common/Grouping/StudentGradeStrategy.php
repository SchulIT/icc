<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Grouping\StudentGradeGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\OptionsAwareGroupInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentGradeStrategy implements GroupingStrategyInterface, OptionsAwareGroupInterface {

    /**
     * @param Student $object
     * @return Grade|null
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getGrade($options['section']);
    }

    /**
     * @param Grade|null $keyA
     * @param Grade|null $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        if($keyA === null && $keyB === null) {
            return true;
        }

        if($keyA === null && $keyB !== null) {
            return false;
        }

        if($keyB === null && $keyA !== null) {
            return false;
        }

        return $keyA->getName() === $keyB->getName();
    }

    /**
     * @param Grade $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new StudentGradeGroup($key);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('section');
        $resolver->setAllowedTypes('section', Section::class);
    }
}