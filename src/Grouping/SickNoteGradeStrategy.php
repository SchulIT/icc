<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\SickNote;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SickNoteGradeStrategy implements GroupingStrategyInterface, OptionsAwareGroupInterface {

    /**
     * @param SickNote $object
     * @return Grade
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getStudent()->getGrade($options['section']);
    }

    /**
     * @param Grade $keyA
     * @param Grade $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new SickNoteGradeGroup($key);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('section');
        $resolver->setAllowedTypes('section', Section::class);
    }
}