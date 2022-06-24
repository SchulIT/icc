<?php

namespace App\Converter;

class FilesizeStringConverter {
    public function convert(int $bytes): string {
        $units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];
        $unit = $units[0];

        for($i = 1; $i < count($units) && $bytes >= 1024; $i++) {
            $bytes /= 1024.0; // force float division
            $unit = $units[$i];
        }

        return sprintf('%s %s', number_format($bytes, 2), $unit);
    }
}