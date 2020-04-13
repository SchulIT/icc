<?php

namespace App\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @deprecated
 */
class QueryStringArgumentValueResolver implements ArgumentValueResolverInterface {

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument) {
        return false;

        $name = $argument->getName();

        if($request->query->has($name) !== true) {
            return false;
        }

        $value = $request->query->get($name);

        if($value === null && $argument->isNullable() !== true) {
            return false;
        }

        if(gettype($value) !== 'string') {
            // values might be either a string or an array of strings -> only support strings
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument) {
        $name = $argument->getName();
        $type = $argument->getType();

        $value = $request->query->get($name);

        if ($value === null && $argument->isNullable() !== true) {
            throw new BadRequestHttpException('Invalid query string supplied.');
        }

        // convert to type
        switch ($type) {
            case 'int':
                $value = intval($value);
                break;

            case 'double':
                $value = doubleval($value);
                break;

            case 'float':
                $value = floatval($value);
                break;

            case 'boolean':
                $value = boolval($value);
                break;
        }

        yield $value;
    }
}