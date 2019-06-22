<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimetableExtension extends AbstractExtension {
    const HexColorRegExp = '/^\#?([0-9a-f]{6})$/s';

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function getFilters() {
        return [
            new TwigFilter('weekday', [ $this, 'getWeekday' ]),
            new TwigFilter('foreground', [ $this, 'getForegroundColor' ])
        ];
    }

    public function getWeekday(int $day) {
        return $this->translator->trans(
            sprintf('date.days.%d', $day)
        );
    }

    public function getForegroundColor(string $backgroudColor) {
        if(!preg_match(static::HexColorRegExp, $backgroudColor)) {
            throw new \InvalidArgumentException(sprintf('Invalid HTML hex color "%s"', $backgroudColor));
        }

        if(substr($backgroudColor, 0, 1) === '#') {
            list($r, $g, $b) = sscanf($backgroudColor, "#%02x%02x%02x");
        } else {
            list($r, $g, $b) = sscanf($backgroudColor, "%02x%02x%02x");
        }

        $intensity = $r*0.299 + $g*0.587 + $b*0.114;

        if($intensity > 186) {
            return 'black';
        }

        return 'white';
    }

}