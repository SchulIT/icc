<?php

namespace App\Command;

use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use ZipArchive;
use function Symfony\Component\String\u;

#[AsCommand('app:backup:create', 'Erstellt ein Backup aller relevanten Daten (Konfigurationsdatei, Datenbank, Zertifikate & hochgeladene Dateien.')]
class BackupCommand extends Command {

    public function __construct(private readonly string $projectPath,
                                private readonly string $databaseDsn,
                                private readonly string $backupDirectory,
                                private readonly string $tempDirectory,
                                private readonly array $files,
                                private readonly array $directories,
                                string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $filename = sprintf('%s/backup-%s.zip', $this->backupDirectory, (new DateTime())->format('Y-m-d-H-i-s'));

        $zip = new ZipArchive();
        if(($error = $zip->open($filename, ZipArchive::CREATE)) !== true) {
            $style->error(sprintf('Fehler (Code: %d): ZIP konnte nicht erstellt werden. Es wurde kein Backup erstellt.', $error));
            return 1;
        }

        $filesToRemove = [ ];

        $style->write('Datenbank-Backup... ');
        $filesToRemove[] = $this->doDatabaseDump($zip);
        $style->writeln('erstellt.');

        foreach($this->files as $file) {
            $this->doFileBackup($zip, $file, $style);
        }

        foreach($this->directories as $directory) {
            $this->doDirectoryBackup($zip, $directory, $style);
        }

        $style->write('ZIP-Datei erstellen... ');
        $zip->close();
        $style->writeln('erstellt.');

        foreach($filesToRemove as $path) {
            unlink($path);
        }

        $style->success(sprintf('Backup erstellt: %s', $filename));

        return 0;
    }

    private function doDatabaseDump(ZipArchive $zip): string {
        $parts = parse_url($this->databaseDsn);
        $output = sprintf('%s/%s.sql', $this->tempDirectory, uniqid());

        $cmd = ['mysqldump'];
        $cmd[] = u($parts['path'])->trimStart('/')->toString();
        $cmd[] = sprintf('--host=%s', $parts['host']);
        $cmd[] = sprintf('--port=%d', $parts['port']);
        $cmd[] = sprintf('--user=%s', $parts['user']);

        if(isset($parts['pass'])) {
            $cmd[] = sprintf('--password=%s', $parts['pass']);
        }

        $cmd[] = sprintf(' > %s', $output);

        $process = Process::fromShellCommandline(implode(' ', $cmd), $this->projectPath, null, null, null);
        $process->run();

        $zip->addFile($output, 'dump.sql');
        return $output;
    }

    private function doFileBackup(ZipArchive $zip, string $file, SymfonyStyle $style): void {
        $realpath = sprintf('%s/%s', $this->projectPath, $file);
        $style->write(sprintf('Sichere Datei %s (%s)... ', $file, $realpath));
        $zip->addFile($realpath, $file);
        $style->writeln('gesichert.');
    }

    private function doDirectoryBackup(ZipArchive $zip, string $directory, SymfonyStyle $style): void {
        $realpath = sprintf('%s/%s', $this->projectPath, $directory);
        $style->write(sprintf('Sichere Verzeichnis %s (%s)... ', $directory, $realpath));
        $finder = new Finder();
        $finder->ignoreVCS(false)->ignoreDotFiles(false);
        foreach($finder->in($realpath)->files() as $file) {
            $zip->addFile($file->getPathname(), sprintf('%s/%s', $directory, $file->getRelativePathname()));
        }

        $style->writeln('gesichert.');
    }
}