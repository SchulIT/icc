<?php

namespace App\Twig;

use App\Entity\MessageFileUpload;
use App\Filesystem\MessageFilesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FilesystemExtension extends AbstractExtension {

    private $messageFilesystem;

    public function __construct(MessageFilesystem $filesystem) {
        $this->messageFilesystem = $filesystem;
    }

    public function getFunctions() {
        return [
            new TwigFunction('user_upload_exists', [ $this, 'userUploadExists'])
        ];
    }

    public function userUploadExists(MessageFileUpload $fileUpload) {
        return $this->messageFilesystem->messageUploadedUserFileExists($fileUpload, $fileUpload->getUser());
    }
}