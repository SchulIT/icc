<?php

namespace App\Http\Attribute;

use App\Http\Controller\ArgumentResolver\DateFromQueryValueResolver;
use Attribute;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[Attribute(Attribute::TARGET_PARAMETER)]
class MapDateFromQuery extends ValueResolver {
    public function __construct(
        public readonly ?string $format = 'Y-m-d',
        public readonly ?string $name = null,
        bool $disabled = false,
        string $resolver = DateFromQueryValueResolver::class
    ) {
        parent::__construct($disabled, $resolver);
    }
}