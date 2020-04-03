<?php

namespace App\Message;

use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use App\Entity\User;
use App\Utils\ArrayUtils;
use Doctrine\Common\Collections\Collection;

class MessageFileUploadView extends AbstractMessageFileView {

    /** @var array<int, MessageFileUpload[]>  */
    private $userUploads;

    public function __construct(array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users, array $userUploads) {
        parent::__construct($students, $studentUsersLookup, $parentUsersLookup, $teachers, $teacherUsersLookup, $users);

        $this->userUploads = $userUploads;
    }

    /**
     * @param User $user
     * @param iterable|MessageFile[] $files
     * @param bool $onlyUploaded Whether or not to only include uploaded files in result (otherwise, dummy elements are created)
     * @return MessageFileUpload[]
     */
    public function getUserUploads(User $user, iterable $files, bool $onlyUploaded = false) {
        $uploads = [ ];

        $userUploads = ArrayUtils::createArrayWithKeys(
            $this->userUploads[$user->getId()] ?? [ ],
            function(MessageFileUpload $upload) {
                return $upload->getMessageFile()->getId();
            }
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
     * @param array $users
     * @param array|MessageFile[]|Collection<MessageFile> $files
     * @return ProgressView
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