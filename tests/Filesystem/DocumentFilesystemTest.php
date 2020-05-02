<?php

namespace App\Tests\Filesystem;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Filesystem\DocumentFilesystem;
use App\Filesystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DocumentFilesystemTest extends TestCase {

    private $documentOneUuid;
    private $documentTwoUuid;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->documentOneUuid = Uuid::fromString('1f1248d4-8742-4b89-a0c4-1f345ce5664a');
        $this->documentTwoUuid = Uuid::fromString('08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e');
    }

    private function getDocument(UuidInterface $uuid) {
        $document = $this->createMock(Document::class);
        $document
            ->method('getUuid')
            ->willReturn($uuid);

        return $document;
    }

    private function getAttachment(UuidInterface $uuid, string $filename, UuidInterface $documentUuid) {
        $attachment = $this->createMock(DocumentAttachment::class);
        $attachment
            ->method('getUuid')
            ->willReturn($uuid);

        $attachment
            ->method('getDocument')
            ->willReturn($this->getDocument($documentUuid));

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
        $flysystem->put('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt', 'bla');
        $flysystem->put('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt', 'foo');
        $flysystem->put('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt', 'bla');
        $flysystem->put('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt', 'foo');

        return $filesystem;
    }

    public function testGetDownloadResponse() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getDownloadResponse($this->getAttachment(Uuid::uuid4(), 'foo.txt', $this->documentOneUuid));
        $this->assertNotNull($response);
    }

    public function testGetDownloadResponseFileNotExistent() {
        $this->expectException(FileNotFoundException::class);

        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getDownloadResponse($this->getAttachment(Uuid::uuid4(), 'non_existing_file.txt', $this->documentOneUuid));
        $this->assertNotNull($response);
    }

    public function testRemoveDocumentDirectory() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentDirectory($this->getDocument($this->documentOneUuid));

        $this->assertFalse($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt'));
        $this->assertFalse($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt'));
    }

    public function testRemoveNonExistingDocumentDirectory() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentDirectory($this->getDocument(Uuid::uuid4()));

        $this->assertTrue($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt'));
        $this->assertTrue($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt'));
    }

    public function testRemoveDocumentAttachment() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeDocumentAttachment($this->getAttachment(Uuid::uuid4(), 'foo.txt', $this->documentOneUuid));
        $this->assertFalse($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt'));
        $this->assertTrue($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt'));
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

        $filesystem->removeDocumentAttachment($this->getAttachment(Uuid::uuid4(), 'non_existing_file.txt', $this->documentOneUuid));
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

        $filesystem->removeDocumentDirectory($this->getDocument(Uuid::uuid4()));
    }
}