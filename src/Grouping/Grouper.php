<?php

namespace App\Grouping;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Grouper implements ContainerAwareInterface {

    /** @var ContainerInterface|null */
    private $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * @param array $items
     * @param string $strategyService
     * @return GroupInterface[]
     */
    public function group(array $items, string $strategyService) {
        if($this->container === null) {
            throw new \RuntimeException('Container was not injected properly');
        }

        $strategy = $this->container->get($strategyService);

        if(!$strategy instanceof GroupingStrategyInterface) {
            throw new \RuntimeException(sprintf('Service "%s" must implement "%s" in order to be used as sorting strategy!', $strategyService, GroupingStrategyInterface::class));
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