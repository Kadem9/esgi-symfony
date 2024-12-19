<?php

namespace App\ValueResolver\Attribute;

use App\ValueResolver\FormSearchValueResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapFormSearch extends ValueResolver
{
    public function __construct(
        public readonly array $searchNames,
        public readonly ?array $defaultSort = null,
        public readonly string $method = Request::METHOD_GET,
        public readonly string $sortName = 'sort',
        public readonly string $sortDirName = 'sortDir'
    )
    {
        parent::__construct(FormSearchValueResolver::class);
    }
}