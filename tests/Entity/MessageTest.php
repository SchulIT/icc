<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Entity\MessageConfirmation;
use App\Entity\MessageFile;
use App\Entity\MessageScope;
use App\Entity\MessageVisibility;
use App\Entity\StudyGroup;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase {
    public function testGettersSetters() {
        $message = new Message();

        $this->assertNull($message->getId());

        $message->setTitle('subject');
        $this->assertEquals('subject', $message->getTitle());

        $message->setContent('content');
        $this->assertEquals('content', $message->getContent());

        $start = new \DateTime('2019-01-01');
        $message->setStartDate($start);
        $this->assertEquals($start, $message->getStartDate());

        $end = new \DateTime('2019-02-01');
        $message->setExpireDate($end);
        $this->assertEquals($end, $message->getExpireDate());

        $scope = MessageScope::Appointments();
        $message->setScope($scope);
        $this->assertEquals($scope, $message->getScope());

        $studyGroup = new StudyGroup();
        $message->addStudyGroup($studyGroup);
        $this->assertTrue($message->getStudyGroups()->contains($studyGroup));

        $message->removeStudyGroups($studyGroup);
        $this->assertFalse($message->getStudyGroups()->contains($studyGroup));

        $attachment = new MessageAttachment();
        $message->addAttachment($attachment);
        $this->assertTrue($message->getAttachments()->contains($attachment));

        $message->removeAttachment($attachment);
        $this->assertFalse($message->getAttachments()->contains($attachment));

        $visibility = new MessageVisibility();
        $message->addVisibility($visibility);
        $this->assertTrue($message->getVisibilities()->contains($visibility));

        $message->removeVisibility($visibility);
        $this->assertFalse($message->getVisibilities()->contains($visibility));

        $message->setIsDownloadsEnabled(true);
        $this->assertTrue($message->isDownloadsEnabled());

        $message->setIsDownloadsEnabled(false);
        $this->assertFalse($message->isDownloadsEnabled());

        $message->setIsUploadsEnabled(true);
        $this->assertTrue($message->isUploadsEnabled());

        $message->setIsUploadsEnabled(false);
        $this->assertFalse($message->isUploadsEnabled());

        $message->setUploadDescription('description');
        $this->assertEquals('description', $message->getUploadDescription());

        $message->setUploadDescription(null);
        $this->assertNull($message->getUploadDescription());

        $file = new MessageFile();
        $message->addFile($file);
        $this->assertTrue($message->getFiles()->contains($file));

        $message->removeFile($file);
        $this->assertFalse($message->getFiles()->contains($file));

        $message->setHiddenFromDashboard(true);
        $this->assertTrue($message->isHiddenFromDashboard());

        $message->setHiddenFromDashboard(false);
        $this->assertFalse($message->isHiddenFromDashboard());

        $message->setIsNotificationSent(true);
        $this->assertTrue($message->isNotificationSent());

        $message->setIsNotificationSent(false);
        $this->assertFalse($message->isNotificationSent());

        $message->setMustConfirm(true);
        $this->assertTrue($message->mustConfirm());

        $message->setMustConfirm(false);
        $this->assertFalse($message->mustConfirm());
    }
}