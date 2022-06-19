<?php

namespace App\Entity;

use App\Validator\NullOrNotBlank;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueEntity(fields={"externalId"})
 */
class Student implements JsonSerializable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotNull()
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotNull()
     * @var string
     */
    private $uniqueIdentifier = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $lastname;

    /**
     * @ORM\Column(type="gender")
     * @var Gender
     */
    private $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @NullOrNotBlank()
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $status;

    /**
     * @ORM\Column(type="date")
     * @var DateTime
     */
    private $birthday;

    /**
     * @ORM\OneToMany(targetEntity="GradeMembership", mappedBy="student", cascade={"persist"}, orphanRemoval=true)
     * @var Collection<GradeMembership>
     */
    private $gradeMemberships;

    /**
     * @ORM\OneToMany(targetEntity="StudyGroupMembership", mappedBy="student", cascade={"persist"}, orphanRemoval=true)
     * @var Collection<StudyGroupMembership>
     */
    private $studyGroupMemberships;

    /**
     * @ORM\ManyToMany(targetEntity="PrivacyCategory")
     * @ORM\JoinTable(name="student_approved_privacy_categories",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<PrivacyCategory>
     */
    private $approvedPrivacyCategories;

    /**
     * @ORM\ManyToMany(targetEntity="Section", cascade={"persist"})
     * @ORM\JoinTable(name="student_sections",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Section>
     */
    private $sections;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->gender = Gender::X();
        $this->studyGroupMemberships = new ArrayCollection();
        $this->approvedPrivacyCategories = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->gradeMemberships = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return Student
     */
    public function setExternalId(?string $externalId): Student {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueIdentifier(): string {
        return $this->uniqueIdentifier;
    }

    /**
     * @param string $uniqueIdentifier
     * @return Student
     */
    public function setUniqueIdentifier(string $uniqueIdentifier): Student {
        $this->uniqueIdentifier = $uniqueIdentifier;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return Student
     */
    public function setFirstname(?string $firstname): Student {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return Student
     */
    public function setLastname(?string $lastname): Student {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return Gender|null
     */
    public function getGender(): ?Gender {
        return $this->gender;
    }

    /**
     * @param Gender|null $gender
     * @return Student
     */
    public function setGender(?Gender $gender): Student {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return Student
     */
    public function setEmail(?string $email): Student {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Student
     */
    public function setStatus(?string $status): Student {
        $this->status = $status;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBirthday(): DateTime {
        return $this->birthday;
    }

    /**
     * @param DateTime $birthday
     * @return Student
     */
    public function setBirthday(DateTime $birthday): Student {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @param DateTime $today
     * @return bool
     */
    public function isFullAged(DateTime $today): bool {
        $diff = date_diff($this->getBirthday(), $today);
        $age = $diff->y;
        return $age >= 18;
    }

    /**
     * @param Section|null $section
     * @return Grade|null
     */
    public function getGrade(?Section $section): ?Grade {
        if($section !== null) {
            /** @var GradeMembership $membership */
            foreach ($this->getGradeMemberships() as $membership) {
                if ($membership->getSection()->getId() === $section->getId()) {
                    return $membership->getGrade();
                }
            }
        }

        return null;
    }

    public function addGradeMembership(GradeMembership $grade): void {
        $this->gradeMemberships->add($grade);
    }

    public function removeGradeMembership(GradeMembership $grade): void {
        $this->gradeMemberships->removeElement($grade);
    }

    /**
     * @return Collection<GradeMembership>
     */
    public function getGradeMemberships(): Collection {
        return $this->gradeMemberships;
    }

    /**
     * @param Grade|null $grade
     * @return Student
     * @deprecated
     */
    public function setGrade(?Grade $grade): Student {
        return $this;
    }

    /**
     * @return Collection<StudyGroupMembership>
     */
    public function getStudyGroupMemberships(): Collection {
        return $this->studyGroupMemberships;
    }

    /**
     * @return Collection<PrivacyCategory>
     */
    public function getApprovedPrivacyCategories(): Collection {
        return $this->approvedPrivacyCategories;
    }

    public function addSection(Section $section): void {
        $this->sections->add($section);
    }

    public function removeSection(Section $section): void {
        $this->sections->removeElement($section);
    }

    /**
     * @return Collection<Section>
     */
    public function getSections(): Collection {
        return $this->sections;
    }

    public function __toString() {
        return sprintf('%s, %s', $this->getLastname(), $this->getFirstname());
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['uuid' => "string", 'firstname' => "null|string", 'lastname' => "null|string", 'email' => "null|string"])] public function jsonSerialize(): array {
        return [
            'uuid' => $this->getUuid()->toString(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'email' => $this->getEmail(),
        ];
    }
}