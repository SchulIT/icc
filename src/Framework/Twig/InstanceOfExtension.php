<?php

declare(strict_types=1);

namespace App\Framework\Twig;

use ReflectionClass;
use Twig\Attribute\AsTwigTest;

class InstanceOfExtension {

    #[AsTwigTest('instanceof')]
    public function isInstanceOf($object, string $className): bool {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->isInstance($object);
    }
}