<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class DocumentVisibility {

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="visibilities", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Document
     */
    private $document;

    /**
     * @ORM\Id()
     * @ORM\Column(type="UserType::class")
     * @var UserType
     */
    private $userType;

    /**
     * @return Document
     */
    public function getDocument(): Document {
        return $this->document;
    }

    /**
     * @param Document $document
     * @return DocumentVisibility
     */
    public function setDocument(Document $document): DocumentVisibility {
        $this->document = $document;
        return $this;
    }

    /**
     * @return UserType
     */
    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @param UserType $userType
     * @return DocumentVisibility
     */
    public function setUserType(UserType $userType): DocumentVisibility {
        $this->userType = $userType;
        return $this;
    }

}