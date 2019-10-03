<?php

namespace App\Tests\Entity;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Entity\DocumentCategory;
use App\Entity\MessageVisibility;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentTest extends WebTestCase {
    public function testGettersSetters() {
        $document = new Document();

        $this->assertNull($document->getId());

        $document->setTitle('name');
        $this->assertEquals('name', $document->getTitle());

        $category = new DocumentCategory();
        $document->setCategory($category);
        $this->assertEquals($category, $document->getCategory());

        $document->setContent('content');
        $this->assertEquals('content', $document->getContent());

        $studyGroup = new StudyGroup();
        $document->addStudyGroup($studyGroup);
        $this->assertTrue($document->getStudyGroups()->contains($studyGroup));

        $document->removeStudyGroup($studyGroup);
        $this->assertFalse($document->getStudyGroups()->contains($studyGroup));

        $attachment = new DocumentAttachment();
        $document->addAttachment($attachment);
        $this->assertTrue($document->getAttachments()->contains($attachment));

        $document->removeAttachment($attachment);
        $this->assertFalse($document->getAttachments()->contains($attachment));

        $visibility = new MessageVisibility();
        $document->addVisibility($visibility);
        $this->assertTrue($document->getVisibilities()->contains($visibility));

        $document->removeVisibility($visibility);
        $this->assertFalse($document->getVisibilities()->contains($visibility));

        $author = new Teacher();
        $document->addAuthor($author);
        $this->assertTrue($document->getAuthors()->contains($author));

        $document->removeAuthor($author);
        $this->assertFalse($document->getAuthors()->contains($author));
    }

    public function testSlug() {
        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $category = (new DocumentCategory())
            ->setName('category');

        $document = (new Document())
            ->setTitle('My fancy document')
            ->setContent('content')
            ->setCategory($category);

        $em->persist($category);
        $em->persist($document);
        $em->flush();

        $this->assertNotNull($document->getAlias());
        $this->assertEquals('my-fancy-document', $document->getAlias());

        $this->assertNotNull($document->getUpdatedAt());
    }
}