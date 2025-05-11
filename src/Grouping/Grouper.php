<?php

namespace App\Grouping;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Grouper {

    /** @var GroupingStrategyInterface[] */
    private array $strategies = [ ];

    /**
     * @param GroupingStrategyInterface[] $strategies
     */
    public function __construct(#[AutowireIterator('app.grouping_strategy')] iterable $strategies) {
        foreach($strategies as $strategy) {
            $this->strategies[$strategy::class] = $strategy;
        }
    }

    /**
     * @return GroupInterface[]
     */
    public function group(array $items, string $strategyService, array $options = [ ]) {
        $strategy = $this->strategies[$strategyService] ?? null;

        if($strategy === null) {
            throw new ServiceNotFoundException($strategyService);
        }

        if($strategy instanceof OptionsAwareGroupInterface) {
            $resolver = new OptionsResolver();
            $strategy->configureOptions($resolver);
            $options = $resolver->resolve($options);
        }

        /** @var GroupInterface[] $groups */
        $groups = [ ];

        foreach($items as $item) {
            $keys = $strategy->computeKey($item, $options);

            if(!is_array($keys)) {
                $keys = [ $keys ];
            }

            foreach($keys as $key) {
                $group = null;

                foreach($groups as $g) {
                    if($strategy->areEqualKeys($g->getKey(), $key, $options)) {
                        $group = $g;
                    }
                }

                if($group === null) {
                    $group = $strategy->createGroup($key, $options);
                    $groups[] = $group;
                }

                $group->addItem($item);
            }
        }

        return $groups;
    }
}