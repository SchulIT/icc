<?php

namespace App\Book\Excuse;

use App\Repository\ExcuseNoteRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class AssociateExcuseNoteMessageHandler {
    public function __construct(
        private ExcuseNoteAssociator $associator,
        private ExcuseNoteRepositoryInterface $excuseNoteRepository
    ) {

    }

    public function __invoke(AssociateExcuseNoteMessage $message): void {
        $excuseNote = $this->excuseNoteRepository->findOneById($message->excuseNoteId);

        if($excuseNote === null) {
            return;
        }

        $this->associator->associateExcuseNote($excuseNote);
    }
}