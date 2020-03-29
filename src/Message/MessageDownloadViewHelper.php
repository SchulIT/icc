<?php

namespace App\Message;

use App\Entity\Message;
use App\Filesystem\MessageFilesystem;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\Collections\Collection;

class MessageDownloadViewHelper extends AbstractMessageFileViewHelper {

    private $messageFilesystem;

    public function __construct(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository, UserRepositoryInterface $userRepository, MessageFilesystem $filesystem) {
        parent::__construct($studentRepository, $teacherRepository, $userRepository);

        $this->messageFilesystem = $filesystem;
    }

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