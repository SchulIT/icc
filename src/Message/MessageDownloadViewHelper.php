<?php

namespace App\Message;

use App\Message\Entity\Message;
use App\Message\Filesystem\MessageFilesystem;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Repository\TeacherRepositoryInterface;
use App\Common\Repository\UserRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use League\Flysystem\FilesystemException;

class MessageDownloadViewHelper extends AbstractMessageFileViewHelper {

    public function __construct(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository, UserRepositoryInterface $userRepository, private MessageFilesystem $messageFilesystem) {
        parent::__construct($studentRepository, $teacherRepository, $userRepository);
    }

    /**
     * @throws FilesystemException
     */
    private function getDownloads(Message $message): array {
        $downloads = [ ];

        foreach($this->messageFilesystem->getAllUserDownloads($message) as $folder) {
            $downloads[$folder['basename']] = $folder['files'];
        }

        return $downloads;
    }

    /**
     * @inheritDoc
     */
    protected function getUserTypes(Message $message): Collection {
        return $message->getDownloadEnabledUserTypes();
    }

    /**
     * @inheritDoc
     */
    protected function getStudyGroups(Message $message): Collection {
        return $message->getDownloadEnabledStudyGroups();
    }

    /**
     * @throws FilesystemException
     */
    protected function createViewFromData(Message $message, array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users): AbstractMessageFileView {
        return new MessageDownloadView(
            $students,
            $studentUsersLookup,
            $parentUsersLookup,
            $teachers,
            $teacherUsersLookup,
            $users,
            $this->getDownloads($message)
        );
    }
}