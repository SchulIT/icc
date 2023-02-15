<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['student', 'lms'])]
#[UniqueEntity(fields: ['student', 'lms'])]
class StudentLearningManagementSystemInformation {

    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'learningManagementSystems')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Student $student;

    #[ORM\ManyToOne(targetEntity: LearningManagementSystem::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?LearningManagementSystem $lms;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isConsented = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isConsentObtained = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isAudioConsented = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isVideoConsented = false;

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return StudentLearningManagementSystemInformation
     */
    public function setStudent(?Student $student): StudentLearningManagementSystemInformation {
        $this->student = $student;
        return $this;
    }

    /**
     * @return LearningManagementSystem|null
     */
    public function getLms(): ?LearningManagementSystem {
        return $this->lms;
    }

    /**
     * @param LearningManagementSystem|null $lms
     * @return StudentLearningManagementSystemInformation
     */
    public function setLms(?LearningManagementSystem $lms): StudentLearningManagementSystemInformation {
        $this->lms = $lms;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return StudentLearningManagementSystemInformation
     */
    public function setUsername(?string $username): StudentLearningManagementSystemInformation {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return StudentLearningManagementSystemInformation
     */
    public function setPassword(?string $password): StudentLearningManagementSystemInformation {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConsented(): bool {
        return $this->isConsented;
    }

    /**
     * @param bool $isConsented
     * @return StudentLearningManagementSystemInformation
     */
    public function setIsConsented(bool $isConsented): StudentLearningManagementSystemInformation {
        $this->isConsented = $isConsented;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConsentObtained(): bool {
        return $this->isConsentObtained;
    }

    /**
     * @param bool $isConsentObtained
     * @return StudentLearningManagementSystemInformation
     */
    public function setIsConsentObtained(bool $isConsentObtained): StudentLearningManagementSystemInformation {
        $this->isConsentObtained = $isConsentObtained;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAudioConsented(): bool {
        return $this->isAudioConsented;
    }

    /**
     * @param bool $isAudioConsented
     * @return StudentLearningManagementSystemInformation
     */
    public function setIsAudioConsented(bool $isAudioConsented): StudentLearningManagementSystemInformation {
        $this->isAudioConsented = $isAudioConsented;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVideoConsented(): bool {
        return $this->isVideoConsented;
    }

    /**
     * @param bool $isVideoConsented
     * @return StudentLearningManagementSystemInformation
     */
    public function setIsVideoConsented(bool $isVideoConsented): StudentLearningManagementSystemInformation {
        $this->isVideoConsented = $isVideoConsented;
        return $this;
    }

    public function __toString(): string {
        return $this->getLms()->getName();
    }
}