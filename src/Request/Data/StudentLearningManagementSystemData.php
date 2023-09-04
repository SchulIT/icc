<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentLearningManagementSystemData {

    /**
     * Importierte ID des Schülers/der Schülerin
     */
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    private ?string $student = null;

    /**
     * Importierte ID der Lernplattform
     */
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    private ?string $lms = null;

    /**
     * Benutzername (optional)
     */
    #[Serializer\Type('string')]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $username = null;

    /**
     * Passwort
     */
    #[Serializer\Type('string')]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $password = null;

    /**
     * Gibt an, ob die Einwilligung erteilt wurde
     */
    #[Serializer\Type('boolean')]
    private bool $isConsented = false;

    /**
     * Gibt an, ob die Einwilligung eingeholt wurde
     */
    #[Serializer\Type('boolean')]
    private bool $isConsentObtained = false;

    /**
     * Gibt an, ob eine Einwilligung für Audiokonferenzen erteilt wurde
     */
    #[Serializer\Type('boolean')]
    private bool $isAudioConsented = false;

    /**
     * Gibt an, ob eine Einwilligung für Videokonferenzen erteilt wurde
     */
    #[Serializer\Type('boolean')]
    private bool $isVideoConsented = false;

    public function getStudent(): ?string {
        return $this->student;
    }

    public function setStudent(?string $student): StudentLearningManagementSystemData {
        $this->student = $student;
        return $this;
    }

    public function getLms(): ?string {
        return $this->lms;
    }

    public function setLms(?string $lms): StudentLearningManagementSystemData {
        $this->lms = $lms;
        return $this;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(?string $username): StudentLearningManagementSystemData {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): StudentLearningManagementSystemData {
        $this->password = $password;
        return $this;
    }

    public function isConsented(): bool {
        return $this->isConsented;
    }

    public function setIsConsented(bool $isConsented): StudentLearningManagementSystemData {
        $this->isConsented = $isConsented;
        return $this;
    }

    public function isConsentObtained(): bool {
        return $this->isConsentObtained;
    }

    public function setIsConsentObtained(bool $isConsentObtained): StudentLearningManagementSystemData {
        $this->isConsentObtained = $isConsentObtained;
        return $this;
    }

    public function isAudioConsented(): bool {
        return $this->isAudioConsented;
    }

    public function setIsAudioConsented(bool $isAudioConsented): StudentLearningManagementSystemData {
        $this->isAudioConsented = $isAudioConsented;
        return $this;
    }

    public function isVideoConsented(): bool {
        return $this->isVideoConsented;
    }

    public function setIsVideoConsented(bool $isVideoConsented): StudentLearningManagementSystemData {
        $this->isVideoConsented = $isVideoConsented;
        return $this;
    }
}