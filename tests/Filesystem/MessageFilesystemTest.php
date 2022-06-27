<?php

namespace App\Tests\Filesystem;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Filesystem\FileNotFoundException;
use App\Filesystem\MessageFilesystem;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MessageFilesystemTest extends TestCase {

    private $messageOneUuid;
    private $messageTwoUuid;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->messageOneUuid = Uuid::fromString('1f1248d4-8742-4b89-a0c4-1f345ce5664a');
        $this->messageTwoUuid = Uuid::fromString('08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e');
    }

    private function getMessage(UuidInterface $uuid) {
        $message = $this->createMock(Message::class);

        $message
            ->method('getUuid')
            ->willReturn($uuid);

        return $message;
    }

    private function getAttachment(UuidInterface $uuid, string $filename, UuidInterface $messageUuid) {
        $attachment = $this->createMock(MessageAttachment::class);

        $attachment
            ->method('getUuid')
            ->willReturn($uuid);

        $attachment
            ->method('getMessage')
            ->willReturn($this->getMessage($messageUuid));

        $attachment
            ->method('getFilename')
            ->willReturn($filename);

        $attachment
            ->method('getPath')
            ->willReturn($filename);

        return $attachment;
    }

    private function getFilesystem(FilesystemOperator $flysystem): MessageFilesystem {
        $filesystem = new MessageFilesystem(new TokenStorage(), $flysystem, new MimeTypes());
        $flysystem->write('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt', 'bla');
        $flysystem->write('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt', 'foo');
        $flysystem->write('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt', 'bla');
        $flysystem->write('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt', 'foo');

        return $filesystem;
    }

    public function testGetDownloadResponse() {
        $flysystem = new Filesystem(new InMemoryFilesystemAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getMessageAttachmentDownloadResponse($this->getAttachment(Uuid::uuid4(), 'foo.txt', $this->messageOneUuid));
        $this->assertNotNull($response);
    }

    public function testGetDownloadResponseFileNotExistent() {
        $this->expectException(FileNotFoundException::class);

        $flysystem = new Filesystem(new InMemoryFilesystemAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getMessageAttachmentDownloadResponse($this->getAttachment(Uuid::uuid4(), 'non_existing_file.txt', $this->messageOneUuid));
        $this->assertNotNull($response);
    }

    public function testRemoveMessageDirectory() {
        $flysystem = new Filesystem(new InMemoryFilesystemAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageDirectoy($this->getMessage($this->messageOneUuid));

        $this->assertFalse($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt'));
        $this->assertFalse($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt'));
    }

    public function testRemoveNonExistingMessageDirectory() {
        $flysystem = new Filesystem(new InMemoryFilesystemAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageDirectoy($this->getMessage(Uuid::uuid4()));

        $this->assertTrue($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt'));
        $this->assertTrue($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt'));
    }

    public function testRemoveMessageAttachment() {
        $flysystem = new Filesystem(new InMemoryFilesystemAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageAttachment($this->getAttachment(Uuid::uuid4(), 'foo.txt', $this->messageOneUuid));
        $this->assertFalse($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/foo.txt'));
        $this->assertTrue($flysystem->has('/1f1248d4-8742-4b89-a0c4-1f345ce5664a/bla.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/foo.txt'));
        $this->assertTrue($flysystem->has('/08d1ab65-3f7c-47e6-abcb-ca2cd8d4fa4e/bla.txt'));
    }

    public function testRemoveNonExistingMessageAttachment() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new InMemoryFilesystemAdapter()])
            ->setMethods(['deleteDir'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('deleteDir');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageAttachment($this->getAttachment(Uuid::uuid4(), 'non_existing_file.txt', $this->messageOneUuid));
    }

    public function testInvalidMessage() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new InMemoryFilesystemAdapter()])
            ->setMethods(['deleteDir'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('deleteDir');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageDirectoy($this->getMessage(Uuid::uuid4()));
    }

}