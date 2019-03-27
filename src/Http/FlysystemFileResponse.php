<?php

namespace App\Http;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FlysystemFileResponse extends StreamedResponse {

    public function __construct(FilesystemInterface $filesystem, string $path, string $filename,
                                string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT) {
        parent::__construct(null, 200, [ ]);

        $this->setCallback(function() use($filesystem, $path) {
            $stream = $filesystem->readStream($path);

            if($stream === false) {
                throw new \RuntimeException(sprintf('Cannot open stream for path "%s"', $path));
            }

            fpassthru($stream);
            flush();
        });

        $this->headers->makeDisposition($disposition, $filename);
    }
}