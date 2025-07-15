<?php

namespace App\Sorting;

use App\Grouping\SortableGroupInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Sorter {

    /** @var SortingStrategyInterface[] */
    private ?array $strategies = null;

    /**
     * @param SortingStrategyInterface[] $strategies
     */
    public function __construct(#[AutowireIterator('app.sorting_strategy')] iterable $strategies) {
        foreach($strategies as $strategy) {
            $this->strategies[$strategy::class] = $strategy;
        }
    }

    /**
     * @param array $groups
     * @param class-string $strategyService
     * @param SortDirection $direction
     * @return void
     */
    public function sortGroupItems(array $groups, string $strategyService, SortDirection $direction = SortDirection::Ascending): void {
        foreach($groups as $group) {
            if($group instanceof SortableGroupInterface) {
                $this->sort($group->getItems(), $strategyService, $direction);
            }
        }
    }

    /**
     * @param array $array
     * @param class-string $strategyService
     * @param SortDirection $direction
     * @param bool $keepIndices
     */
    public function sort(array &$array, string $strategyService, SortDirection $direction = SortDirection::Ascending, bool $keepIndices = false): void {
        $strategy = $this->strategies[$strategyService] ?? null;

        if($strategy === null) {
            throw new ServiceNotFoundException($strategyService);
        }

        if($keepIndices === true) {
            uasort($array, [$strategy, 'compare']);
        } else {
            usort($array, [$strategy, 'compare']);
        }

        if(SortDirection::Descending === $direction) {
            $array = array_reverse($array);
        }
    }
}