<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Chat\Entity\ChatMessage;
use App\ParentsDay\Entity\ParentsDayParentalInformation;
use App\StudentAbsence\Entity\StudentAbsenceMessage;
use App\LearningManagementSystem\Entity\StudentLearningManagementSystemInformation;
use App\TeacherAbsence\Entity\TeacherAbsence;
use App\Document\Sorting\DocumentCategoryNameStrategy;
use Defuse\Crypto\Crypto;
use Doctrine\ORM\EntityManagerInterface;
use SensitiveParameter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:database:decrypt', description: 'Entschlüsselt alle Datenbankspalten bei Bedarf')]
readonly class DecryptDatabaseCommand {

    public const string EncryptionMaker = '<ENC>';
    private const int BatchSize = 50;

    public function __construct(
        #[Autowire(env: 'DB_SECRET'), SensitiveParameter] private string $secret,
        private EntityManagerInterface $entityManager,
    ) { }

    public function __invoke(SymfonyStyle $io): int {
        if(empty($this->secret)) {
            $io->error('DB_SECRET ist nicht gesetzt, nichts zu entschlüsseln.');
            return Command::INVALID;
        }

        $this->decryptTeacherAbsences($io);
        $this->decryptStudentLearningManagementSystemInformation($io);
        $this->decryptStudentAbsenceMessages($io);
        $this->decryptParentsDayParentalInformation($io);
        $this->decryptChatMessages($io);

        return Command::SUCCESS;
    }

    private function decryptAndSave(string $tableName, string $columnName, string $content, int $id): void {
        $this->entityManager->getConnection()->update(
            $tableName,
            [
                $columnName => Crypto::decryptWithPassword(
                    substr($content, 0, -strlen(self::EncryptionMaker)),
                    base64_decode($this->secret)
                )
            ],
            [
                'id' => $id
            ]
        );
    }

    private function getTableName(string $entityFqcn): string {
        $metadata = $this->entityManager->getClassMetadata($entityFqcn);
        return $metadata->getTableName();
    }

    private function decryptTeacherAbsences(SymfonyStyle $io): void {
        $io->section('Entschlüssle Absenzen von Lehrkräften');

        /** @var TeacherAbsence[] $absences */
        $absences = $this->entityManager->getRepository(TeacherAbsence::class)->findAll();
        $progress = new ProgressBar($io, count($absences));

        $i = 0;
        $tableName = $this->getTableName(TeacherAbsence::class);
        $this->entityManager->getConnection()->beginTransaction();

        foreach($absences as $absence) {
            $progress->advance();

            if($absence->getMessage() === null || !str_ends_with($absence->getMessage(), self::EncryptionMaker)) {
                continue;
            }

            $this->decryptAndSave(
                $tableName,
                'message',
                $absence->getMessage(),
                $absence->getId()
            );

            if($i % self::BatchSize === 0) {
                $this->entityManager->getConnection()->commit();
                $this->entityManager->beginTransaction();
            }

            $i++;
        }

        $this->entityManager->getConnection()->commit();
        $this->entityManager->clear();

        $progress->finish();
        $io->success('Alle entschlüsselt');
    }



    private function decryptStudentLearningManagementSystemInformation(SymfonyStyle $io): void {
        $io->section('Entschlüssle LMS-Zugangsdaten');

        /** @var StudentLearningManagementSystemInformation[] $information */
        $information = $this->entityManager->getRepository(StudentLearningManagementSystemInformation::class)->findAll();
        $progress = new ProgressBar($io, count($information));

        $i = 0;
        $tableName = $this->getTableName(StudentLearningManagementSystemInformation::class);
        $this->entityManager->getConnection()->beginTransaction();

        foreach($information as $info) {
            $progress->advance();

            if($info->getPassword() === null || !str_ends_with($info->getPassword(), self::EncryptionMaker)) {
                continue;
            }

            $this->decryptAndSave(
                $tableName,
                'password',
                $info->getPassword(),
                $info->getId()
            );

            if($i % self::BatchSize === 0) {
                $this->entityManager->getConnection()->commit();
                $this->entityManager->beginTransaction();
            }

            $i++;
        }

        $this->entityManager->getConnection()->commit();
        $this->entityManager->clear();

        $progress->finish();
        $io->success('Alle entschlüsselt');
    }

    private function decryptStudentAbsenceMessages(SymfonyStyle $io): void {
        $io->section('Entschlüssle Schülerabsenzen');
        /** @var StudentAbsenceMessage[] $messages */
        $messages = $this->entityManager->getRepository(StudentAbsenceMessage::class)->findAll();
        $progress = new ProgressBar($io, count($messages));

        $i = 0;
        $tableName = $this->getTableName(StudentAbsenceMessage::class);
        $this->entityManager->getConnection()->beginTransaction();

        foreach($messages as $message) {
            $progress->advance();

            if($message->getMessage() === null || !str_ends_with($message->getMessage(), self::EncryptionMaker)) {
                continue;
            }

            $this->decryptAndSave(
                $tableName,
                'message',
                $message->getMessage(),
                $message->getId()
            );

            if($i % self::BatchSize === 0) {
                $this->entityManager->getConnection()->commit();
                $this->entityManager->beginTransaction();
            }

            $i++;
        }

        $this->entityManager->getConnection()->commit();
        $this->entityManager->clear();

        $progress->finish();
        $io->success('Alle entschlüsselt');
    }

    private function decryptParentsDayParentalInformation(SymfonyStyle $io): void {
        $io->section('Entschlüssle Elternsprechtagsinformationen');

        /** @var ParentsDayParentalInformation[] $information */
        $information = $this->entityManager->getRepository(ParentsDayParentalInformation::class)->findAll();
        $progress = new ProgressBar($io, count($information));

        $i = 0;
        $tableName = $this->getTableName(ParentsDayParentalInformation::class);
        $this->entityManager->getConnection()->beginTransaction();

        foreach($information as $info) {
            $progress->advance();

            if($info->getComment() === null || !str_ends_with($info->getComment(), self::EncryptionMaker)) {
                continue;
            }

            $this->decryptAndSave(
                $tableName,
                'comment',
                $info->getComment(),
                $info->getId()
            );

            if($i % self::BatchSize === 0) {
                $this->entityManager->getConnection()->commit();
                $this->entityManager->beginTransaction();
            }

            $i++;
        }

        $this->entityManager->getConnection()->commit();
        $this->entityManager->clear();

        $progress->finish();
        $io->success('Alle entschlüsselt');
    }

    private function decryptChatMessages(SymfonyStyle $io): void {
        $io->section('Entschlüssle private Nachrichten');

        /** @var ChatMessage[] $messages */
        $messages = $this->entityManager->getRepository(ChatMessage::class)->findAll();
        $progress = new ProgressBar($io, count($messages));

        $i = 0;
        $tableName = $this->getTableName(ChatMessage::class);
        $this->entityManager->getConnection()->beginTransaction();

        foreach($messages as $message) {
            $progress->advance();

            if($message->getContent() === null || !str_ends_with($message->getContent(), self::EncryptionMaker)) {
                continue;
            }

            $this->decryptAndSave(
                $tableName,
                'content',
                $message->getContent(),
                $message->getId()
            );

            if($i % self::BatchSize === 0) {
                $this->entityManager->getConnection()->commit();
                $this->entityManager->beginTransaction();
            }

            $i++;
        }

        $this->entityManager->getConnection()->commit();
        $this->entityManager->clear();

        $progress->finish();
        $io->success('Alle entschlüsselt');
    }
}
