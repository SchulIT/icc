<?php

namespace App\Message;

use App\Entity\MessageFile;
use App\Entity\User;

class MessageDownloadView extends AbstractMessageFileView {

    /** @var array<int, string[]>  */
    private $downloads;

    public function __construct(array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users, array $downloads) {
        parent::__construct($students, $studentUsersLookup, $parentUsersLookup, $teachers, $teacherUsersLookup, $users);

        $this->downloads = $downloads;
    }

    /**
     * @param User $user
     * @return array[]
     */
    public function getUserDownloads(User $user) {
        return $this->downloads[$user->getUsername()] ?? [ ];
    }
}