<?php

namespace App\Command;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Filesystem\DocumentFilesystem;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:filesystem:documents:cleanup', description: 'Räumt den Ordner files/documents/ auf und synchronisiert ihn mit der Datenbank.')]
readonly class CleanupDocumentFilesystemCommand {
    public function __construct(private FilesystemOperator $documentsFilesystem,
                                private DocumentFilesystem $appFilesystem,
                                private EntityManagerInterface $em) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output, #[Option('Nur prüfen und nichts löschen.', 'dry-run', 'd')] bool $dryRun = false): int {
        if($dryRun === true) {
            $style->info('Diese Operation wird als `dry-run` ausgeführt. Es wird nichts gelöscht.');
        }

        $style->section('Anhänge (Dokumente) aufräumen');
        $attachments = $this->em->getRepository(DocumentAttachment::class)->findAll();

        $style->section('Prüfe Anhänge in Datenbank...');
        foreach($attachments as $attachment) {
            $path = $this->appFilesystem->getAttachmentPath($attachment);

            $style->write(sprintf('Prüfe %s: ', $path));

            if($this->documentsFilesystem->fileExists($path)) {
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

        $documents = $this->em->getRepository(Document::class)->findAll();

        foreach($documents as $document) {
            $directory = $this->appFilesystem->getAttachmentsDirectory($document);
            foreach($this->documentsFilesystem->listContents($directory) as $item) {
                if(!$item instanceof FileAttributes) {
                    continue;
                }

                $basename = basename($item->path());
                $attachment = $document->getAttachments()->findFirst(fn(int $key, DocumentAttachment $attachment) => $attachment->getPath() === $basename);

                $style->write(sprintf('Prüfe %s/%s: ', $directory, $basename));

                if($attachment === null) {
                    if($dryRun) {
                        $style->writeln('nicht in Datenbank vorhanden. Unternehme nichts (dry-run).');
                    } else {
                        $style->writeln('nicht in Datenbank vorhanden. Lösche Datei.');
                        $this->documentsFilesystem->delete(sprintf('%s/%s', $directory, $basename));
                    }
                } else {
                    $style->writeln('in Datenbank vorhanden. Unternehme nichts.');
                }
            }
        }


        $style->section('Lösche verwaiste Dokumentenverzeichnisse');

        foreach($this->documentsFilesystem->listContents('/') as $item) {
            if(!$item instanceof DirectoryAttributes) {
                continue;
            }

            $uuid = basename($item->path());

            $style->write(sprintf('Prüfe UUID %s: ', $uuid));

            $chat = $this->em->getRepository(Document::class)->findOneBy(['uuid' => $uuid]);

            if($chat === null) {
                if($dryRun) {
                    $style->writeln('nicht in Datenbank vorhanden. Unternehme nichts (dry-run).');
                } else {
                    $style->writeln('nicht in Datenbank vorhanden. Lösche Ordner.');
                    $this->documentsFilesystem->deleteDirectory($item->path());
                }
            } else {
                $style->writeln('in Datenbank vorhanden. Unternehme nichts.');
            }
        }

        return Command::SUCCESS;
    }
}