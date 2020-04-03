<?php

namespace App\Tests\Filesystem;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Filesystem\MessageFilesystem;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MessageFilesystemTest extends TestCase {

    private function getMessage(?int $id) {
        $message = $this->createMock(Message::class);

        $message
            ->method('getId')
            ->willReturn($id);

        return $message;
    }

    private function getAttachment(?int $id, string $filename, ?int $messageId) {
        $attachment = $this->createMock(MessageAttachment::class);

        $attachment
            ->method('getId')
            ->willReturn($id);

        $attachment
            ->method('getMessage')
            ->willReturn($this->getMessage($messageId));

        $attachment
            ->method('getFilename')
            ->willReturn($filename);

        $attachment
            ->method('getPath')
            ->willReturn($filename);

        return $attachment;
    }

    private function getFilesystem(FilesystemInterface $flysystem): MessageFilesystem {
        $filesystem = new MessageFilesystem(new TokenStorage(), $flysystem, new MimeTypes());
        $flysystem->put('/1/foo.txt', 'bla');
        $flysystem->put('/1/bla.txt', 'foo');
        $flysystem->put('/2/foo.txt', 'bla');
        $flysystem->put('/2/bla.txt', 'foo');

        return $filesystem;
    }

    public function testGetDownloadResponse() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getMessageAttachmentDownloadResponse($this->getAttachment(1, 'foo.txt', 1));
        $this->assertNotNull($response);
    }

    /**
     * @expectedException App\Filesystem\FileNotFoundException
     */
    public function testGetDownloadResponseFileNotExistent() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $response = $filesystem->getMessageAttachmentDownloadResponse($this->getAttachment(1, 'non_existing_file.txt', 1));
        $this->assertNotNull($response);
    }

    public function testRemoveMessageDirectory() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageDirectoy($this->getMessage(1));

        $this->assertFalse($flysystem->has('/1/foo.txt'));
        $this->assertFalse($flysystem->has('/1/bla.txt'));
        $this->assertTrue($flysystem->has('/2/foo.txt'));
        $this->assertTrue($flysystem->has('/2/bla.txt'));
    }

    public function testRemoveNonExistingMessageDirectory() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageDirectoy($this->getMessage(42));

        $this->assertTrue($flysystem->has('/1/foo.txt'));
        $this->assertTrue($flysystem->has('/1/bla.txt'));
        $this->assertTrue($flysystem->has('/2/foo.txt'));
        $this->assertTrue($flysystem->has('/2/bla.txt'));
    }

    public function testRemoveMessageAttachment() {
        $flysystem = new Filesystem(new MemoryAdapter());
        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageAttachment($this->getAttachment(1, 'foo.txt', 1));
        $this->assertFalse($flysystem->has('/1/foo.txt'));
        $this->assertTrue($flysystem->has('/1/bla.txt'));
        $this->assertTrue($flysystem->has('/2/foo.txt'));
        $this->assertTrue($flysystem->has('/2/bla.txt'));
    }

    public function testRemoveNonExistingMessageAttachment() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new MemoryAdapter()])
            ->setMethods(['deleteDir'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('deleteDir');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageAttachment($this->getAttachment(1, 'non_existing_file.txt', 1));
    }

    public function testInvalidMessage() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new MemoryAdapter()])
            ->setMethods(['deleteDir'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('deleteDir');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageDirectoy($this->getMessage(null));
    }

    public function testInvalidMessageAttachment() {
        $flysystem = $this->getMockBuilder(Filesystem::class)
            ->setConstructorArgs([new MemoryAdapter()])
            ->setMethods(['delete'])
            ->getMock();
        $flysystem
            ->expects($this->never())
            ->method('delete');

        $filesystem = $this->getFilesystem($flysystem);

        $filesystem->removeMessageAttachment($this->getAttachment(null, 'file.txt', 1));
        $filesystem->removeMessageAttachment($this->getAttachment(null, 'file.txt', null));
        $filesystem->removeMessageAttachment($this->getAttachment(1, 'file.txt', null));


        $attachment = $this->getAttachment(1, 'file.txt', null);
        $attachment
            ->method('getMessage')
            ->willReturn(null);

        $filesystem->removeMessageAttachment($attachment);
    }
}