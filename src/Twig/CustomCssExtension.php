<?php

namespace App\Twig;

use App\Settings\GeneralSettings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CustomCssExtension extends AbstractExtension {

    public function __construct(private readonly GeneralSettings $settings) {}

    public function getFunctions(): array {
        return [
            new TwigFunction('customCSS', [$this, 'getCustomCSS']),
        ];
    }

    public function getCustomCSS(): ?string {
        return $this->settings->getCustomCSS();
    }
}