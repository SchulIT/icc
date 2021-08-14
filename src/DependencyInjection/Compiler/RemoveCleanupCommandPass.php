<?php

namespace App\DependencyInjection\Compiler;

use SchulIT\CommonBundle\Command\PruneCronjobResultsCommand;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveCleanupCommandPass implements CompilerPassInterface {

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container) {
        $container->removeDefinition(PruneCronjobResultsCommand::class);
    }
}