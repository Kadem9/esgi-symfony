<?php

namespace App\ValueResolver\Attribute;

use App\ValueResolver\UserPreferenceValueResolver;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class GetUserPreference extends ValueResolver
{
    public function __construct(
        public readonly string $preferenceKey
    )
    {
        parent::__construct(UserPreferenceValueResolver::class);
    }
}