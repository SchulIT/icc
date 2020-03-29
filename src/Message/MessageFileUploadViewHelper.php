<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use Doctrine\Common\Collections\Collection;

class MessageFileUploadViewHelper extends AbstractMessageFileViewHelper {
    /**
     * Returns the uploads for all users.
     *
     * @param Message $message
     * @return array<int, MessageFileUpload[]> Key is the user's ID
     */
    private function getUploads(Message $message): array {
        $uploads = [ ];

        /** @var MessageFile $file */
        foreach($message->getFiles() as $file) {
            /** @var MessageFileUpload $upload */
            foreach($file->getUploads() as $upload) {
                if(!isset($uploads[$upload->getUser()->getId()])) {
                    $uploads[$upload->getUser()->getId()] = [ ];
                }

                $uploads[$upload->getUser()->getId()][] = $upload;
            }
        }

        return $uploads;
    }

    /**
     * @inheritDoc
     */
    protected function getUserTypes(Message $message): Collection {
        return $message->getUploadEnabledUserTypes();
    }

    /**
     * @inheritDoc
     */
    protected function getStudyGroups(Message $message): Collection {
        return $message->getUploadEnabledStudyGroups();
    }

    protected function createViewFromData(Message $message, array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users): AbstractMessageFileView {
        return new MessageFileUploadView(
            $students,
            $studentUsersLookup,
            $parentUsersLookup,
            $teachers,
            $teacherUsersLookup,
            $users,
            $this->getUploads($message)
        );
    }
}