<?php

namespace App\Message;

use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use App\Entity\User;
use App\Utils\ArrayUtils;
use Doctrine\Common\Collections\Collection;

class MessageFileUploadView extends AbstractMessageFileView {

    /**
     * @param array<int, MessageFileUpload[]> $userUploads
     */
    public function __construct(array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users, private array $userUploads) {
        parent::__construct($students, $studentUsersLookup, $parentUsersLookup, $teachers, $teacherUsersLookup, $users);
    }

    /**
     * @param iterable|MessageFile[] $files
     * @param bool $onlyUploaded Whether or not to only include uploaded files in result (otherwise, dummy elements are created)
     * @return MessageFileUpload[]
     */
    public function getUserUploads(User $user, iterable $files, bool $onlyUploaded = false) {
        $uploads = [ ];

        $userUploads = ArrayUtils::createArrayWithKeys(
            $this->userUploads[$user->getId()] ?? [ ],
            fn(MessageFileUpload $upload) => $upload->getMessageFile()->getId()
        );

        foreach($files as $file) {
            $upload = $userUploads[$file->getId()] ?? (new MessageFileUpload())->setMessageFile($file);

            if($upload->isUploaded() || $onlyUploaded === false) {
                $uploads[] = $upload;
            }
        }

        return $uploads;
    }

    /**
     * @param array|MessageFile[]|Collection<MessageFile> $files
     */
    public function getProgress(array $users, iterable $files): ProgressView {
        $progress = 0;
        $total = count($users) * count($files);

        foreach($users as $user) {
            $progress += count($this->getUserUploads($user, $files, true));
        }

        return new ProgressView($progress, $total);
    }

    public function getStudentProgress(array $students, iterable $files): ProgressView {
        $users = [ ];

        foreach($students as $student) {
            $users = array_merge($users, $this->getStudentUsers($student));
        }

        return $this->getProgress($users, $files);
    }

    public function getParentProgress(array $students, iterable $files): ProgressView {
        $users = [ ];

        foreach($students as $student) {
            $users = array_merge($users, $this->getParentUsers($student));
        }

        return $this->getProgress($users, $files);
    }

    public function getTeacherProgress(array $teachers, iterable $files): ProgressView {
        $users = [ ];

        foreach($teachers as $teacher) {
            $users = array_merge($users, $this->getTeacherUsers($teacher));
        }

        return $this->getProgress($users, $files);
    }
}