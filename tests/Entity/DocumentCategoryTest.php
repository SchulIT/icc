<?php

namespace App\Tests\Entity;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentCategoryTest extends WebTestCase {
    public function testGettersSetters() {
        $category = new DocumentCategory();

        $this->assertNull($category->getId());

        $category->setName('name');
        $this->assertEquals('name', $category->getName());
    }

    public function testDocuments() {
        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()->get('doctrine')
            ->getManager();

        $category = (new DocumentCategory())
            ->setName('category');

        $document = (new Document())
            ->setContent('content')
            ->setTitle('name')
            ->setCategory($category);

        $em->persist($category);
        $em->persist($document);
        $em->flush();

        $em->detach($category);

        /** @var DocumentCategory $category */
        $category = $em->getRepository(DocumentCategory::class)
            ->findOneBy([
                'id' => $category->getId()
            ]);

        $this->assertEquals(1, $category->getDocuments()->count());
        $this->assertEquals($document->getId(), $category->getDocuments()->first()->getId());
    }
}