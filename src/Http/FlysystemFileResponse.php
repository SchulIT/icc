<?php

namespace App\Http;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FlysystemFileResponse extends StreamedResponse {

    public function __construct(FilesystemOperator $filesystem, string $path, string $filename, $mime = 'application/octet-stream',
                                string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT) {
        parent::__construct(null, 200, [ ]);

        $this->headers->set('Content-Type', $mime);

        $this->setCallback(function() use($filesystem, $path) {
            $stream = $filesystem->readStream($path);
            fpassthru($stream);
            flush();
        });

        $this->headers->set('Content-Disposition', $this->headers->makeDisposition($disposition, $filename, transliterator_transliterate('Latin-ASCII', $filename)));
    }
}