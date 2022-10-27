<?php

namespace App;

use App\DependencyInjection\Compiler\RemoveCleanupCommandPass;
use App\DependencyInjection\Compiler\RemoveDumpCommandPass;
use App\DependencyInjection\Compiler\RemovePcntlEventSubscriberPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function build(ContainerBuilder $container) {
        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $container->addCompilerPass(new RemoveDumpCommandPass());
        $container->addCompilerPass(new RemoveCleanupCommandPass());
        $container->addCompilerPass(new RemovePcntlEventSubscriberPass());
    }
}
