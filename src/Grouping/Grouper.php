<?php

namespace App\Grouping;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Grouper {

    /** @var GroupingStrategyInterface[] */
    private $strategies = [ ];

    /**
     * @param GroupingStrategyInterface[]
     */
    public function __construct(iterable $strategies) {
        foreach($strategies as $strategy) {
            $this->strategies[get_class($strategy)] = $strategy;
        }
    }

    /**
     * @param array $items
     * @param string $strategyService
     * @return GroupInterface[]
     */
    public function group(array $items, string $strategyService) {
        $strategy = $this->strategies[$strategyService] ?? null;

        if($strategy === null) {
            throw new ServiceNotFoundException($strategyService);
        }

        /** @var GroupInterface[] $groups */
        $groups = [ ];

        foreach($items as $item) {
            $keys = $strategy->computeKey($item);

            if(!is_array($keys)) {
                $keys = [ $keys ];
            }

            foreach($keys as $key) {
                $group = null;

                foreach($groups as $g) {
                    if($strategy->areEqualKeys($g->getKey(), $key)) {
                        $group = $g;
                    }
                }

                if($group === null) {
                    $group = $strategy->createGroup($key);
                    $groups[] = $group;
                }

                $group->addItem($item);
            }
        }

        return $groups;
    }
}