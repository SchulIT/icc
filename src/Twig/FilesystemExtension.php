<?php

namespace App\Twig;

use App\Entity\MessageFileUpload;
use App\Filesystem\MessageFilesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FilesystemExtension extends AbstractExtension {

    public function __construct(private MessageFilesystem $messageFilesystem)
    {
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('user_upload_exists', [ $this, 'userUploadExists'])
        ];
    }

    public function userUploadExists(MessageFileUpload $fileUpload): bool {
        return $this->messageFilesystem->messageUploadedUserFileExists($fileUpload, $fileUpload->getUser());
    }
}