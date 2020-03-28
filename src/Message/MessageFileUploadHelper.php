<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Entity\User;
use App\Repository\MessageFileUploadRepositoryInterface;

class MessageFileUploadHelper {
    private $messageFileUploadRepository;

    public function __construct(MessageFileUploadRepositoryInterface $messageFileUploadRepository) {
        $this->messageFileUploadRepository = $messageFileUploadRepository;
    }

    /**
     * Returns the missing files which a user has to upload.
     *
     * @param Message $message
     * @param User $user
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