<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
#[UniqueEntity(fields: ['externalId'])]
class Student implements JsonSerializable, Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[Assert\NotNull]
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[Assert\NotNull]
    private ?string $uniqueIdentifier = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $firstname = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $lastname = null;

    /**
     * @ORM\Column(type="gender")
     */
    private ?Gender $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $status = null;

    /**
     * @ORM\Column(type="date")
     */
    private ?\DateTime $birthday = null;

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

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Student {
        $this->externalId = $externalId;
        return $this;
    }

    public function getUniqueIdentifier(): string {
        return $this->uniqueIdentifier;
    }

    public function setUniqueIdentifier(string $uniqueIdentifier): Student {
        $this->uniqueIdentifier = $uniqueIdentifier;
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): Student {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): Student {
        $this->lastname = $lastname;
        return $this;
    }

    public function getGender(): ?Gender {
        return $this->gender;
    }

    public function setGender(?Gender $gender): Student {
        $this->gender = $gender;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): Student {
        $this->email = $email;
        return $this;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(?string $status): Student {
        $this->status = $status;
        return $this;
    }

    public function getBirthday(): DateTime {
        return $this->birthday;
    }

    public function setBirthday(DateTime $birthday): Student {
        $this->birthday = $birthday;
        return $this;
    }

    public function isFullAged(DateTime $today): bool {
        $diff = date_diff($this->getBirthday(), $today);
        $age = $diff->y;
        return $age >= 18;
    }

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

    public function __toString(): string {
        return sprintf('%s, %s', $this->getLastname(), $this->getFirstname());
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array {
        return [
            'uuid' => $this->getUuid()->toString(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'email' => $this->getEmail(),
        ];
    }
}