<?php

namespace App\Message;

use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Utils\ArrayUtils;

class MessageFileUploadView {

    /** @var Student[] */
    private $students;

    /** @var Teacher[] */
    private $teachers;

    /** @var array<int, User[]>  */
    private $studentUsersLookup;
    /** @var array<int, User[]> */
    private $parentUsersLookup;
    /** @var array<int, User[]>  */
    private $teacherUsersLookup;
    /** @var array<int, User>  */
    private $users;

    /** @var array<int, MessageFileUpload[]>  */
    private $userUploads;

    public function __construct(array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users, array $userUploads) {
        $this->students = $students;
        $this->studentUsersLookup = $studentUsersLookup;
        $this->parentUsersLookup = $parentUsersLookup;
        $this->teachers = $teachers;
        $this->teacherUsersLookup = $teacherUsersLookup;
        $this->users = $users;

        $this->userUploads = $userUploads;
    }

    public function getStudents() {
        return $this->students;
    }

    public function getTeachers() {
        return $this->teachers;
    }

    public function getUsers() {
        return array_values($this->users);
    }

    public function getStudentUsers(Student $student) {


        return $this->studentUsersLookup[$student->getId()] ?? [ ];
    }

    public function getParentUsers(Student $student) {
        return $this->parentUsersLookup[$student->getId()] ??  [ ];
    }

    public function getTeacherUsers(Teacher $teacher) {
        return $this->teacherUsersLookup[$teacher->getId()] ?? [ ];
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
            $uploads[] = $userUploads[$file->getId()] ?? (new MessageFileUpload())->setMessageFile($file);
        }

        return $uploads;
    }

    /**
     * @param array $users
     * @param array|MessageFile[]|Collection<MessageFile> $files
     * @return float
     */
    public function getProgress(array $users, iterable $files): float {
        $progress = 0;
        $total = count($users) * count($files);

        foreach($users as $user) {
            $progress += count($this->getUserUploads($user, $files, true));
        }

        if($total === 0) {
            return 0;
        }

        return round((float)$progress / $total * 100);
    }

    public function getStudentProgress(array $students, iterable $files): float {
        $users = [ ];

        foreach($students as $student) {
            $users = array_merge($users, $this->getStudentUsers($student));
        }

        return $this->getProgress($users, $files);
    }

    public function getParentProgress(array $students, iterable $files): float {
        $users = [ ];

        foreach($students as $student) {
            $users = array_merge($users, $this->getParentUsers($student));
        }

        return $this->getProgress($users, $files);
    }

    public function getTeacherProgress(array $teachers, iterable $files): float {
        $users = [ ];

        foreach($teachers as $teacher) {
            $users = array_merge($users, $this->getTeachers($teacher));
        }

        return $this->getProgress($users, $files);
    }
}