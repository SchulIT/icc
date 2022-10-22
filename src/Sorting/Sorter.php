<?php

namespace App\Sorting;

use App\Grouping\SortableGroupInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Sorter {

    /** @var SortingStrategyInterface[] */
    private ?array $strategies = null;

    /**
     * @param SortingStrategyInterface[] $strategies
     */
    public function __construct(iterable $strategies) {
        foreach($strategies as $strategy) {
            $this->strategies[$strategy::class] = $strategy;
        }
    }

    public function sortGroupItems(array $groups, string $strategyService, SortDirection $direction = null) {
        foreach($groups as $group) {
            if($group instanceof SortableGroupInterface) {
                $this->sort($group->getItems(), $strategyService, $direction);
            }
        }
    }

    /**
     * @param SortDirection|null $direction
     */
    public function sort(array &$array, string $strategyService, SortDirection $direction = null, bool $keepIndices = false) {
        $strategy = $this->strategies[$strategyService] ?? null;

        if($strategy === null) {
            throw new ServiceNotFoundException($strategyService);
        }

        if($keepIndices === true) {
            uasort($array, [$strategy, 'compare']);
        } else {
            usort($array, [$strategy, 'compare']);
        }

        if(SortDirection::Descending()->equals($direction)) {
            $array = array_reverse($array);
        }
    }
}