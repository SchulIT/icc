<?php

namespace App\Framework\Twig;

use App\Framework\Converter\EnumStringConverter;
use App\Framework\Converter\FilesizeStringConverter;
use App\Framework\Converter\TimestampDateTimeConverter;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConverterExtension extends AbstractExtension {
    public function __construct(
        private readonly FilesizeStringConverter $filesizeConverter,
        private readonly TimestampDateTimeConverter $timestampConverter,
        private readonly EnumStringConverter $enumStringConverter,
    ) { }

    public function getFilters(): array {
        return [

            new TwigFilter('filesize', [ $this, 'filesize' ]),
            new TwigFilter('todatetime', [ $this, 'toDateTime' ]),
            new TwigFilter('enum', [ $this, 'enum']),

        ];
    }

    public function filesize(int $bytes): string {
        return $this->filesizeConverter->convert($bytes);
    }

    public function toDateTime(int $timestamp): DateTime {
        return $this->timestampConverter->convert($timestamp);
    }

    public function enum($enum): string {
        return $this->enumStringConverter->convert($enum);
    }
}
