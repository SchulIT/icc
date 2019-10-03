<?php

namespace App\Tests\Filesystem;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Filesystem\DocumentFilesystem;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;

class DocumentFilesystemTest extends TestCase {

    private function getDocument(?int $id) {
        $document = $this->createMock(Document::class);
        $document
            ->method('getId')
            ->willReturn($id);

        return $document;
    }

    private function getAttachment(?int $id, string $filename, ?int $documentId) {
        $attachment = $this->createMock(DocumentAttachment::class);
        $attachment
            ->method('getId')
            ->willReturn($id);

        $attachment
            ->method('getDocument')
            ->willReturn($this->getDocument($documentId));

        $attachment
            ->method('getFilename')
            ->willReturn($filename);

        $attachment
            ->method('getPath')
            ->willReturn($filename);

        return $attachment;
    }

    private function getFilesystem(FilesystemInterface $flysystem): DocumentFilesystem {
        $filesystem = new DocumentFilesystem($flysystem, new MimeTypes());
        $flysystem->put('/1/foo.txt', 'bla');
        $flysystem->put('/1/bla.txt', 'foo');
        $flysystem->put('/2/foo.txt', 'bla');
        $flysystem->put('/2/bla.txt', 'foo');

        return $filesystem;
    }

    public function testGetDownloadResponse() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getDownloadResponse($this->getAttachment(1, 'foo.txt', 1));
        $this->assertNotNull($response);
    }

    /**
     * @expectedException App\Filesystem\FileNotFoundException
     */
    public function testGetDownloadResponseFileNotExistent() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getDownloadResponse($this->getAttachment(1, 'non_existing_file.txt', 1));
        $this->assertNotNull($response);
    }

    public function testRemoveDocumentDirectory() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentDirectory($this->getDocument(1));

        $this->assertFalse($flysystem->has('/1/foo.txt'));
        $this->assertFalse($flysystem->has('/1/bla.txt'));
        $this->assertTrue($flysystem->has('/2/foo.txt'));
        $this->assertTrue($flysystem->has('/2/bla.txt'));
    }

    public function testRemoveNonExistingDocumentDirectory() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentDirectory($this->getDocument(42));

        $this->assertTrue($flysystem->has('/1/foo.txt'));
        $this->assertTrue($flysystem->has('/1/bla.txt'));
        $this->assertTrue($flysystem->has('/2/foo.txt'));
        $this->assertTrue($flysystem->has('/2/bla.txt'));
    }

    public function testRemoveDocumentAttachment() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentAttachment($this->getAttachment(1, 'foo.txt', 1));
        $this->assertFalse($flysystem->has('/1/foo.txt'));
        $this->assertTrue($flysystem->has('/1/bla.txt'));
        $this->assertTrue($flysystem->has('/2/foo.txt'));
        $this->assertTrue($flysystem->has('/2/bla.txt'));
    }

    public function testRemoveNonExistingDocumentAttachment() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new MemoryAdapter()])
            ->setMethods(['deleteDir'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('deleteDir');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentAttachment($this->getAttachment(1, 'non_existing_file.txt', 1));
    }

    public function testInvalidDocument() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new MemoryAdapter()])
            ->setMethods(['deleteDir'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('deleteDir');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentDirectory($this->getDocument(null));
    }

    public function testInvalidDocumentAttachment() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new MemoryAdapter()])
            ->setMethods(['delete'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('delete');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentAttachment($this->getAttachment(null, 'file.txt', 1));
        $filesystem->removeDocumentAttachment($this->getAttachment(null, 'file.txt', null));
        $filesystem->removeDocumentAttachment($this->getAttachment(1, 'file.txt', null));


        $attachment = $this->getAttachment(1, 'file.txt', null);
        $attachment
            ->method('getDocument')
            ->willReturn(null);

        $filesystem->removeDocumentAttachment($attachment);
    }
}