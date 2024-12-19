<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FrontExtension extends AbstractExtension
{

    public function __construct(private readonly string $iconVersion, private readonly string $nameApp)
    {
    }

    public function getFunctions() : array
    {
        return [
            new TwigFunction('front_icon', [$this, 'frontIcon'], ['is_safe' => ['html']]),
            new TwigFunction('name_app', [$this, 'getNameApp']),

        ];
    }

    public function frontIcon(string $icon, bool $small = false) : string
    {
        $class = ["icon", "icon-{$icon}"];
        if($small) $class[] = "icon-sm";
        $classStr = implode(" ", $class);
        return <<<HTML
        <svg class="{$classStr}">
            <use xlink:href="/front_icons.svg?v={$this->iconVersion}&logo#{$icon}"></use>
        </svg>
        HTML;
    }

    public function getNameApp(): string
    {
        return $this->nameApp;
    }
}