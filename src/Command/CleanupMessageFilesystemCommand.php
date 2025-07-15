<?php

namespace App\Command;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Filesystem\MessageFilesystem;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:filesystem:messages:cleanup', description: 'Räumt den Ordner files/messages/ auf und synchronisiert ihn mit der Datenbank.')]
readonly class CleanupMessageFilesystemCommand {
    public function __construct(private FilesystemOperator $messagesFilesystem,
                                private MessageFilesystem $appFilesystem,
                                private EntityManagerInterface $em) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output, #[Option('Nur prüfen und nichts löschen.', 'dry-run', 'd')] bool $dryRun = false): int {
        if($dryRun === true) {
            $style->info('Diese Operation wird als `dry-run` ausgeführt. Es wird nichts gelöscht.');
        }

        $style->section('Anhänge (Mitteilungen) aufräumen');
        $attachments = $this->em->getRepository(MessageAttachment::class)->findAll();

        $style->section('Prüfe Anhänge in Datenbank...');
        foreach($attachments as $attachment) {
            $path = $this->appFilesystem->getMessageAttachmentPath($attachment);

            $style->write(sprintf('Prüfe %s: ', $path));

            if($this->messagesFilesystem->fileExists($path)) {
                $style->writeln('existiert. Unternehme nichts.');
            } else if($dryRun) {
                $style->writeln('existiert nicht. Unternehme nichts (dry-run).');
            } else {
                $style->writeln('existiert nicht. Lösche aus Datenbank.');
                $this->em->remove($attachment);
            }
        }

        $this->em->flush();

        $style->section('Lösche verwaiste Anhänge vom Dateisystem');

        $messages = $this->em->getRepository(Message::class)->findAll();
        $messagesInDatabase = [ ];

        foreach($messages as $message) {
            $messagesInDatabase[] = $message->getUuid();

            $directory = $this->appFilesystem->getMessageDirectory($message);
            foreach($this->messagesFilesystem->listContents($directory) as $item) {
                if(!$item instanceof FileAttributes) {
                    continue;
                }

                $basename = basename($item->path());
                $attachment = $message->getAttachments()->findFirst(fn(int $key, MessageAttachment $attachment) => $attachment->getPath() === $basename);

                $style->write(sprintf('Prüfe %s/%s: ', $directory, $basename));

                if($attachment === null) {
                    if($dryRun) {
                        $style->writeln('nicht in Datenbank vorhanden. Unternehme nichts (dry-run).');
                    } else {
                        $style->writeln('nicht in Datenbank vorhanden. Lösche Datei.');
                        $this->messagesFilesystem->delete(sprintf('%s/%s', $directory, $basename));
                    }
                } else {
                    $style->writeln('in Datenbank vorhanden. Unternehme nichts.');
                }
            }
        }

        $style->section('Lösche verwaiste Mitteilungsverzeichnisse');

        foreach($this->messagesFilesystem->listContents('/') as $item) {
            if(!$item instanceof DirectoryAttributes) {
                continue;
            }

            $uuid = basename($item->path());

            $style->write(sprintf('Prüfe UUID %s: ', $uuid));

            if(!in_array($uuid, $messagesInDatabase)) {
                if($dryRun) {
                    $style->writeln('nicht in Datenbank vorhanden. Unternehme nichts (dry-run).');
                } else {
                    $style->writeln('nicht in Datenbank vorhanden. Lösche Ordner.');
                    $this->messagesFilesystem->deleteDirectory($item->path());
                }
            } else {
                $style->writeln('in Datenbank vorhanden. Unternehme nichts.');
            }
        }

        return Command::SUCCESS;
    }
}