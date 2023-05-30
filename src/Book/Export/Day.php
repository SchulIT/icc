<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Day {

    #[Serializer\SerializedName('date')]
    #[Serializer\Type('DateTime')]
    private ?DateTime $date = null;

    /**
     * @var Lesson[]
     */
    #[Serializer\SerializedName('lessons')]
    #[Serializer\Type('array<App\Book\Export\Lesson>')]
    private array $lessons = [ ];

    /**
     * @var Comment[]
     */
    #[Serializer\SerializedName('comments')]
    #[Serializer\Type('array<App\Book\Export\Comment>')]
    private array $comments = [ ];

    public function getDate(): DateTime {
        return $this->date;
    }

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