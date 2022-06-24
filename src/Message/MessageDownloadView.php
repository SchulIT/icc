<?php

namespace App\Message;

use App\Entity\MessageFile;
use App\Entity\User;

class MessageDownloadView extends AbstractMessageFileView {

    /** @var array<string, string[]>  */
    private array $downloads;

    public function __construct(array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users, array $downloads) {
        parent::__construct($students, $studentUsersLookup, $parentUsersLookup, $teachers, $teacherUsersLookup, $users);

        $this->downloads = $downloads;
    }

    /**
     * @param User $user
     * @return string[]
     */
    public function getUserDownloads(User $user): array {
        return $this->downloads[$user->getUsername()] ?? [ ];
    }
}