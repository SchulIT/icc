<?php

namespace App\Command;

use App\Entity\Chat;
use App\Entity\ChatMessageAttachment;
use App\Filesystem\ChatFilesystem;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:filesystem:chats:cleanup', description: 'Räumt den Ordner files/chats/ auf und synchronisiert ihn mit der Datenbank.')]
readonly class CleanupChatFilesystemCommand {
    public function __construct(private FilesystemOperator $chatFilesystem,
                                private ChatFilesystem $appFilesystem,
                                private EntityManagerInterface $em) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output, #[Option('Nur prüfen und nichts löschen.', 'dry-run', 'd')] bool $dryRun = false): int {
        if($dryRun === true) {
            $style->info('Diese Operation wird als `dry-run` ausgeführt. Es wird nichts gelöscht.');
        }

        $style->section('Anhänge (Chats) aufräumen');
        $attachments = $this->em->getRepository(ChatMessageAttachment::class)->findAll();

        $style->section('Prüfe Anhänge in Datenbank...');
        foreach($attachments as $attachment) {
            $path = $this->appFilesystem->getPath($attachment);

            $style->write(sprintf('Prüfe %s: ', $path));

            if($this->chatFilesystem->fileExists($path)) {
                $style->writeln('existiert. Unternehme nichts.');
            } else if($dryRun) {
                $style->writeln('existiert nicht. Unternehme nichts (dry-run).');
            } else {
                $style->writeln('existiert nicht. Lösche aus Datenbank.');
                $this->em->remove($attachment);
            }
        }

        $this->em->flush();

        $style->section('Lösche verwaiste Chatverzeichnisse');

        foreach($this->chatFilesystem->listContents('/') as $item) {
            if(!$item instanceof DirectoryAttributes) {
                continue;
            }

            $uuid = basename($item->path());

            $style->write(sprintf('Prüfe UUID %s: ', $uuid));

            $chat = $this->em->getRepository(Chat::class)->findOneBy(['uuid' => $uuid]);

            if($chat === null) {
                if($dryRun) {
                    $style->writeln('nicht in Datenbank vorhanden. Unternehme nichts (dry-run).');
                } else {
                    $style->writeln('nicht in Datenbank vorhanden. Lösche Ordner.');
                    $this->chatFilesystem->deleteDirectory($item->path());
                }
            } else {
                $style->writeln('in Datenbank vorhanden. Unternehme nichts.');
            }
        }

        return Command::SUCCESS;
    }
}