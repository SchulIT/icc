<?php

namespace App\Grouping;

use App\Exception\UnexpectedTypeException;
use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class GenericDateStrategy implements GroupingStrategyInterface, OptionsAwareGroupInterface {

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor) {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('allow_null', false);
        $resolver->setRequired('group_class');
        $resolver->setAllowedTypes('group_class', ['string', 'callable']);
    }

    /**
     * @inheritDoc
     * @throws UnexpectedTypeException
     */
    public function computeKey($object, array $options = []) {
        $dateTime = $this->propertyAccessor->getValue($object, 'date');

        if($dateTime === null && $options['allow_null'] === true) {
            return null;
        }

        if(!$dateTime instanceof DateTime) {
            throw new UnexpectedTypeException($dateTime, DateTime::class);
        }

        return $dateTime;
    }

    /**
     * @param DateTime|null $keyA
     * @param DateTime|null $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = []): bool {
        return $keyA == $keyB;
    }

    /**
     * @inheritDoc
     */
    public function createGroup($key, array $options = []): GroupInterface {
        $groupClass = $options['group_class'];
        if(is_callable($groupClass)) {
            return $groupClass($key);
        }

        return new $groupClass($key);
    }


}