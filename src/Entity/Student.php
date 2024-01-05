<?php

namespace App\Entity;

use App\Repository\LearningManagementSystemRepositoryInterface;
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

#[Auditable]
#[UniqueEntity(fields: ['externalId'])]
#[ORM\Entity]
class Student implements JsonSerializable, Stringable {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $externalId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', enumType: Gender::class)]
    private ?Gender $gender;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $status = null;

    #[ORM\Column(type: 'date')]
    private ?DateTime $birthday = null;

    /**
     * @var Collection<GradeMembership>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: GradeMembership::class, cascade: ['persist'], orphanRemoval: true)]
    private $gradeMemberships;

    /**
     * @var Collection<GradeLimitedMembership>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: GradeLimitedMembership::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $gradeLimitedMemberships;

    /**
     * @var Collection<StudyGroupMembership>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: StudyGroupMembership::class, cascade: ['persist'], orphanRemoval: true)]
    private $studyGroupMemberships;

    /**
     * @var Collection<PrivacyCategory>
     */
    #[ORM\JoinTable(name: 'student_approved_privacy_categories')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: PrivacyCategory::class)]
    private $approvedPrivacyCategories;

    /**
     * @var Collection<Section>
     */
    #[ORM\JoinTable(name: 'student_sections')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Section::class, cascade: ['persist'])]
    private $sections;

    /**
     * @var Collection<StudentLearningManagementSystemInformation>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: StudentLearningManagementSystemInformation::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $learningManagementSystems;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->gender = Gender::X;
        $this->studyGroupMemberships = new ArrayCollection();
        $this->approvedPrivacyCategories = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->gradeMemberships = new ArrayCollection();
        $this->gradeLimitedMemberships = new ArrayCollection();
        $this->learningManagementSystems = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Student {
        $this->externalId = $externalId;
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

    public function getBirthday(): ?DateTime {
        return $this->birthday;
    }

    public function setBirthday(?DateTime $birthday): Student {
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
     * @return Collection<GradeLimitedMembership>
     */
    public function getGradeLimitedMemberships():Collection {
        return $this->gradeLimitedMemberships;
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

    public function addLearningManagementSystem(StudentLearningManagementSystemInformation $info): void {
        $info->setStudent($this);
        $this->learningManagementSystems->add($info);
    }

    public function removeLearningManagementSystem(StudentLearningManagementSystemInformation $info): void {
        $this->learningManagementSystems->removeElement($info);
    }

    /**
     * @return Collection<StudentLearningManagementSystemInformation>
     */
    public function getLearningManagementSystems(): Collection {
        return $this->learningManagementSystems;
    }

    public function getLearningManagementSystemInfo(LearningManagementSystem $lms): ?StudentLearningManagementSystemInformation {
        return $this->learningManagementSystems->findFirst(fn(int $idx, StudentLearningManagementSystemInformation $info) => $info->getLms()->getId() === $lms->getId());
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