<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Day {

    /**
     * @Serializer\SerializedName("date")
     * @Serializer\Type("DateTime")
     * @var DateTime
     */
    private $date;

    /**
     * @Serializer\SerializedName("lessons")
     * @Serializer\Type("array<App\Book\Export\Lesson>")
     * @var Lesson[]
     */
    private $lessons = [ ];

    /**
     * @Serializer\SerializedName("comments")
     * @Serializer\Type("array<App\Book\Export\Comment>")
     * @var Comment[]
     */
    private $comments = [ ];

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Day
     */
    public function setDate(DateTime $date): Day {
        $this->date = $date;
        return $this;
    }

    public function addLesson(Lesson $lesson): void {
        $this->lessons[] = $lesson;
    }

    /**
     * @return Lesson[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

    public function addComment(Comment $comment): void {
        $this->comments[] = $comment;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array {
        return $this->comments;
    }
}