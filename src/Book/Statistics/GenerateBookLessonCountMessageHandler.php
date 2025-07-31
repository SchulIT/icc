<?php

namespace App\Book\Statistics;

use App\Repository\TuitionRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GenerateBookLessonCountMessageHandler {

    public function __construct(private BookLessonCountGenerator $generator, private TuitionRepositoryInterface $tuitionRepository) {

    }

    public function __invoke(GenerateBookLessonCountMessage $message): void {
        $tuition = $this->tuitionRepository->findOneById($message->getTuitionId());

        if($tuition === null) {
            return;
        }

        $this->generator->regenerate($tuition);
    }
}