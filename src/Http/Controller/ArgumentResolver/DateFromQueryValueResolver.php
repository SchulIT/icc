<?php

namespace App\Http\Controller\ArgumentResolver;

use App\Http\Attribute\MapDateFromQuery;
use DateTimeImmutable;
use DateTimeInterface;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ValueError;

class DateFromQueryValueResolver implements ValueResolverInterface {

    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable {
        if(!$attribute = $argument->getAttributesOfType(MapDateFromQuery::class)[0] ?? null) {
            return [ ];
        }

        $name = $attribute->name ?? $argument->getName();
        $class = DateTimeInterface::class === $argument->getType() ? DateTimeImmutable::class : $argument->getType();

        if(!$request->query->has($name)) {
            if($argument->isNullable() || $argument->hasDefaultValue()) {
                return [ ];
            }

            throw HttpException::fromStatusCode(Response::HTTP_BAD_REQUEST, sprintf('Missing query parameter "%s".', $name));
        }

        $value = $request->query->getString($name);
        $type = $argument->getType();

        if(!is_subclass_of($type, DateTimeInterface::class)) {
            throw HttpException::fromStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR, sprintf('Param type must be subclass of "%s", "%s" given.', DateTimeInterface::class, $type));
        }

        try {
            $date = $class::createFromFormat($attribute->format, $value);
            $date->setTime(0, 0, 0);

            return [ $date ];
        } catch (ValueError) {
            throw HttpException::fromStatusCode(Response::HTTP_BAD_REQUEST, sprintf('Malformed date value: "%s" given.', $value));
        }
    }
}