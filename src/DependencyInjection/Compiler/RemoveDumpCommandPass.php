<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This removes the dump command due to incompatiblity reasons.
 */
class RemoveDumpCommandPass implements CompilerPassInterface {

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container) {
        $container->removeDefinition('nelmio_api_doc.command.dump');
    }
}