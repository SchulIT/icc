<?php

namespace App\Message;

use App\Message\Entity\Message;
use App\Message\Entity\MessageFile;
use App\Common\Entity\User;
use App\Message\Repository\MessageFileUploadRepositoryInterface;

class MessageFileUploadHelper {
    public function __construct(private MessageFileUploadRepositoryInterface $messageFileUploadRepository)
    {
    }

    /**
     * Returns the missing files which a user has to upload.
     *
     * @return MessageFile[]
     */
    public function getMissingUploadedFiles(Message $message, User $user) {
        $missing = [ ];

        /** @var MessageFile $file */
        foreach($message->getFiles() as $file) {
            $upload = $this->messageFileUploadRepository->findOneByFileAndUser($file, $user);

            if($upload === null) {
                $missing[] = $file;
            }
        }

        return $missing;
    }
}