<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use ZipArchive;
use function Symfony\Component\String\u;

#[AsCommand('app:backup:restore', description: 'Stellt ein Backup wieder her.')]
class RestoreCommand extends Command {

    public function __construct(private readonly string $projectPath,
                                private readonly string $databaseDsn,
                                private readonly string $backupDirectory,
                                private readonly string $tempDirectory,
                                private readonly array $files,
                                private readonly array $directories,
                                private readonly EntityManagerInterface $em,
                                string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);
        $style->section('Backup auswählen');

        $backups = [ ];
        $finder = new Finder();
        foreach($finder->files()->depth(0)->name('*.zip')->in($this->backupDirectory) as $file) {
            $backups[] = $file->getBasename();
        }

        $backupToRestore = $style->choice('Backup auswählen, das wiederhergestellt werden soll', $backups);

        $style->caution('BEIM WIEDERHERSTELLEN WERDEN ALLE DATEN GELÖSCHT');
        $confirm = $style->confirm(sprintf('Soll das Backup %s wirklich wiederhergestellt werden? ALLE DATEN WERDEN GELÖSCHT', $backupToRestore), false);

        if($confirm !== true) {
            $style->warning('Backup abgebrochen. Es wurden keine Daten gelöscht.');
            return Command::SUCCESS;
        }

        $zip = new ZipArchive();
        if($zip->open(sprintf('%s/%s', $this->backupDirectory, $backupToRestore)) !== true) {
            $style->error('ZIP-Datei konnte nicht geöffnet werden.');
            return Command::FAILURE;
        }

        $style->section(sprintf('Backup %s wiederherstellen', $backupToRestore));

        $style->writeln('ZIP-Archiv extrahieren');
        $extractDir = sprintf('%s/%s', $this->tempDirectory, uniqid());
        $zip->extractTo($extractDir);

        $this->restoreDatabase($extractDir, $style);
        $this->restoreFiles($extractDir, $style);
        $this->restoreDirectories($extractDir, $style);

        @rmdir($extractDir);

        $style->success('Backup erfolgreich wiederhergestellt');

        return Command::SUCCESS;
    }

    private function restoreDatabase(string $extractDir, SymfonyStyle $style): void {
        $style->writeln('Lösche aktuelle Datenbank');

        $schemaManager = new SchemaTool($this->em);
        $schemaManager->dropSchema($this->em->getMetadataFactory()->getAllMetadata());

        $style->writeln('Spiele Datenbank aus Backup ein');

        $input = sprintf('%s/%s', $extractDir, 'dump.sql');
        $parts = parse_url($this->databaseDsn);

        $cmd = ['mysql'];
        $cmd[] = sprintf('--host=%s', $parts['host']);
        $cmd[] = sprintf('--port=%d', $parts['port']);
        $cmd[] = sprintf('--user=%s', $parts['user']);

        if(isset($parts['pass'])) {
            $cmd[] = sprintf('--password=%s', $parts['pass']);
        }

        $cmd[] = u($parts['path'])->trimStart('/')->toString();
        $cmd[] = sprintf(' < %s', $input);

        $process = Process::fromShellCommandline(implode(' ', $cmd), $this->projectPath);
        $process->run();

        unlink($input);
    }

    private function restoreFiles(string $extractDir, SymfonyStyle $style): void {
        foreach($this->files as $file) {
            $source = sprintf('%s/%s', $extractDir, $file);
            $target = sprintf('%s/%s', $this->projectPath, $file);

            if(!file_exists($source)) {
                $style->writeln(sprintf('Überspringe %s, da nicht im Backup enthalten', $file));
                continue;
            }

            if(file_exists($target)) {
                unlink($target);
            }

            $style->writeln(sprintf('Ersetze Datei %s', $file));
            rename($source, $target);
        }
    }

    private function restoreDirectories(string $extractDir, SymfonyStyle $style): void {
        $filesystem = new Filesystem();

        foreach($this->directories as $directory) {
            $source = sprintf('%s/%s', $extractDir, $directory);
            $target = sprintf('%s/%s', $this->projectPath, $directory);

            if(!is_dir($source)) {
                $style->writeln(sprintf('Überspringe %s/, da nicht im Backup enthalten', $directory));
                continue;
            }

            if(is_dir($target)) {
                $filesystem->remove($target);
            }

            $filesystem->rename($source, $target);
            $style->writeln(sprintf('Ersetze Ordner %s/', $directory));
        }
    }
}