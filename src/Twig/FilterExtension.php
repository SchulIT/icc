<?php

namespace App\Twig;

use App\Entity\Section;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FilterExtension extends AbstractExtension {

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor) {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function getFilters(): array {
        return [
            new TwigFilter('only_section', [ $this, 'filterCurrentSection'])
        ];
    }

    public function filterCurrentSection(iterable $collection, Section $section): array {
        $result = [ ];

        foreach($collection as $item) {
            if($this->propertyAccessor->getValue($item, 'section') === $section) {
                $result[] = $item;
            }
        }

        return $result;
    }
}