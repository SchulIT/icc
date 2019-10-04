<?php

namespace App\Utils;

class ColorUtils {

    private const Threshold = 150;

    public function getForeground(string $background): string {
        if(substr($background, 0, 1) === '#') {
            $background = substr($background, 1);
        }

        list($r, $g, $b) = array_map(function($color) {
            return hexdec($color);
        }, str_split($background, 2));

        $luminance = $this->computeLuminance($r, $g, $b);

        if($luminance < static::Threshold) {
            return 'white';
        }

        return 'black';
    }

    /**
     * Computes the luminance for a given color (see https://www.w3.org/TR/WCAG20/#relativeluminancedef)
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @return float
     */
    private function computeLuminance(int $r, int $g, int $b): float {
        return 0.2126*$r + 0.7152*$g + 0.0722*$b;
    }
}