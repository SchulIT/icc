<?php

namespace App\Twig;

use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WebpackEntryCssSourceExtension extends AbstractExtension {

    public function __construct(private readonly EntrypointLookupInterface $entrypointLookup, private readonly string $publicDir) {

    }

    public function getFunctions() {
        return [
            new TwigFunction('css_source', [ $this, 'getCssSource'])
        ];
    }

    public function getCssSource(string $entryName): string {
        $files = $this->entrypointLookup->getCssFiles($entryName);
        $source = '';

        foreach($files as $file) {
            $source .= file_get_contents($this->publicDir . $file);
        }

        return $source;
    }
}