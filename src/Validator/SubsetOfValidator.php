<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;

class SubsetOfValidator extends AbstractComparisonValidator {

    /**
     * Note:
     * - $value1 is expected to be the value of the property which the constraint is applied to.
     * - $value2 is the value of the property which it is compared to (comes from propertyPath)
     *
     * @inheritDoc
     * @param iterable $value1 All values in this iterable must be present in $value2
     * @param iterable $value2
     */
    protected function compareValues($value1, $value2) {
        foreach($value1 as $subsetValue) {
            foreach($value2 as $supersetValue) {
                if($subsetValue === $supersetValue) {
                    // $subsetValue was found in $value2
                    continue 2;
                }
            }

            // $subsetValue was not found in $value2
            return false;
        }

        return true;
    }
}