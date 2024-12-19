<?php

namespace App\ValueResolver;

use App\Service\User\CurrentUserService;
use App\Service\User\UserPreferenceService;
use App\ValueResolver\Attribute\GetUserPreference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class UserPreferenceValueResolver implements ValueResolverInterface
{

    public function __construct(private UserPreferenceService $userPreferenceService, private CurrentUserService $currentUserService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute  = $argument->getAttributes(GetUserPreference::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if(!($attribute instanceof GetUserPreference)) {
            return [];
        }

        $userPreference = $this->userPreferenceService->getPreference($this->currentUserService->getCurrentUser()->getId(), $attribute->preferenceKey) ?? [];

        return [
            $userPreference
        ];
    }
}