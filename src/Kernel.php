<?php

namespace App;

use Acelaya\Doctrine\Type\PhpEnumType;
use App\Entity\Gender;
use App\Entity\GradeTeacherType;
use App\Entity\MessageScope;
use App\Entity\StudentStatus;
use App\Entity\StudyGroupType;
use App\Entity\UserType;
use App\Entity\WikiArticleVisibility;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/version'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }

    public function boot() {
        $enums = [
            'Gender::class' => Gender::class,
            'GradeTeacherType::class' => GradeTeacherType::class,
            'MessageScope::class' => MessageScope::class,
            'StudentStatus::class' => StudentStatus::class,
            'StudyGroupType::class' => StudyGroupType::class,
            'UserType::class' => UserType::class
        ];

        foreach($enums as $alias => $enum) {
            if(!PhpEnumType::hasType($alias)) {
                PhpEnumType::registerEnumType($alias, $enum);
            }
        }

        return parent::boot();
    }
}
