<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\SickNote;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SickNoteGradeStrategy implements GroupingStrategyInterface, OptionsAwareGroupInterface {

    /**
     * @param SickNote $object
     * @return Grade|null
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getStudent()->getGrade($options['section']);
    }

    /**
     * @param Grade|null $keyA
     * @param Grade|null $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        if($keyA === null && $keyB === null) {
            return true;
        }

        if(($keyA === null && $keyB !== null) || ($keyA !== null && $keyB === null)) {
            return false;
        }

        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade $key
     * @param array $options
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