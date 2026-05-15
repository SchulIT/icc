<?php

namespace App\Tests\Entity;

use App\Document\Entity\Document;
use App\Document\Entity\DocumentAttachment;
use App\Document\Entity\DocumentCategory;
use App\Common\Entity\Grade;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\Teacher;
use App\Common\Entity\UserTypeEntity;
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

        $grade = new Grade();
        $document->addGrade($grade);
        $this->assertTrue($document->getGrades()->contains($grade));

        $document->removeGrade($grade);
        $this->assertFalse($document->getGrades()->contains($grade));

        $attachment = new DocumentAttachment();
        $document->addAttachment($attachment);
        $this->assertTrue($document->getAttachments()->contains($attachment));

        $document->removeAttachment($attachment);
        $this->assertFalse($document->getAttachments()->contains($attachment));

        $visibility = new UserTypeEntity();
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
}