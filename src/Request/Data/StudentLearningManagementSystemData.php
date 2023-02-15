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
    private ?string $student;

    /**
     * Importierte ID der Lernplattform
     */
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    private ?string $lms;

    /**
     * Benutzername (optional)
     */
    #[Serializer\Type('string')]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $username = null;

    /**
     * Passwort
     */
    #[Serializer\Type('string')]
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

    /**
     * @return string|null
     */
    public function getStudent(): ?string {
        return $this->student;
    }

    /**
     * @param string|null $student
     * @return StudentLearningManagementSystemData
     */
    public function setStudent(?string $student): StudentLearningManagementSystemData {
        $this->student = $student;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLms(): ?string {
        return $this->lms;
    }

    /**
     * @param string|null $lms
     * @return StudentLearningManagementSystemData
     */
    public function setLms(?string $lms): StudentLearningManagementSystemData {
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
     * @return StudentLearningManagementSystemData
     */
    public function setUsername(?string $username): StudentLearningManagementSystemData {
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
     * @return StudentLearningManagementSystemData
     */
    public function setPassword(?string $password): StudentLearningManagementSystemData {
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
     * @return StudentLearningManagementSystemData
     */
    public function setIsConsented(bool $isConsented): StudentLearningManagementSystemData {
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
     * @return StudentLearningManagementSystemData
     */
    public function setIsConsentObtained(bool $isConsentObtained): StudentLearningManagementSystemData {
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
     * @return StudentLearningManagementSystemData
     */
    public function setIsAudioConsented(bool $isAudioConsented): StudentLearningManagementSystemData {
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
     * @return StudentLearningManagementSystemData
     */
    public function setIsVideoConsented(bool $isVideoConsented): StudentLearningManagementSystemData {
        $this->isVideoConsented = $isVideoConsented;
        return $this;
    }
}