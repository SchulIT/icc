<?php

namespace App;

use App\Infrastructure\DependencyInjection\Compiler\RemoveDumpCommandPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function build(ContainerBuilder $container): void {
        $container->addCompilerPass(new RemoveDumpCommandPass());
        $this->loadEntityMapping($container, __DIR__, __NAMESPACE__);
    }

    private function loadEntityMapping(ContainerBuilder $container, string $rootDir, string $rootNamespace): void {
        $directories = Finder::create()
            ->in($rootDir)
            ->directories()
            ->name('Entity');

        foreach ($directories as $directory) {
            $namespace = $rootNamespace . '\\' . str_replace('/', '\\', $directory->getRelativePathname());
            $container->addCompilerPass(DoctrineOrmMappingsPass::createAttributeMappingDriver(
                [ $namespace ],
                [ $directory->getRealPath() ]
            ));
        }
    }
}
