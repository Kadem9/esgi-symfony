<?php

namespace App\ValueResolver;

use App\Helper\FormSearchHelper;
use App\ValueResolver\Attribute\MapFormSearch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class FormSearchValueResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute  = $argument->getAttributes(MapFormSearch::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if(!($attribute instanceof MapFormSearch)) {
            return [];
        }

        $formSearch = new FormSearchHelper($attribute->searchNames, $request, $attribute->defaultSort, $attribute->method, $attribute->sortName, $attribute->sortDirName);

        return [
            $formSearch
        ];
    }
}