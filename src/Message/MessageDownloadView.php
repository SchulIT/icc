<?php

namespace App\Message;

use App\Entity\MessageFile;
use App\Entity\User;

class MessageDownloadView extends AbstractMessageFileView {

    /**
     * @param array<string, string[]> $downloads
     */
    public function __construct(array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users, private array $downloads) {
        parent::__construct($students, $studentUsersLookup, $parentUsersLookup, $teachers, $teacherUsersLookup, $users);
    }

    /**
     * @return string[]
     */
    public function getUserDownloads(User $user): array {
        return $this->downloads[$user->getUsername()] ?? [ ];
    }
}