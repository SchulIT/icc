<?php

namespace App\Entity;

use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class StudentAbsenceType implements Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $details = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $bookLabel = null;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $mustApprove = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isTypeWithZeroAbsenceLessons = false;

    #[ORM\Column(type: 'integer', enumType: AttendanceType::class)]
    private AttendanceType $bookAttendanceType = AttendanceType::Absent;

    #[ORM\Column(type: 'integer', enumType: AttendanceExcuseStatus::class)]
    private AttendanceExcuseStatus $bookExcuseStatus = AttendanceExcuseStatus::NotExcused;

    #[ORM\Column(type: 'json')]
    #[Assert\All([
        new Assert\NotBlank(),
        new Assert\Email()
    ])]
    private array $additionalRecipients = [ ];

    /**
     * @var Collection<Subject>
     */
    #[ORM\JoinTable(name: 'student_absence_type_subjects')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Subject::class)]
    private Collection $subjects;

    #[ORM\Column(type: 'boolean')]
    private bool $notifySubjectTeacher = false;

    /**
     * @var Collection<AttendanceFlag>
     */
    #[ORM\JoinTable(name: 'student_absence_type_flags')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: AttendanceFlag::class)]
    private Collection $flags;

    /**
     * @var Collection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'student_absence_type_allowed_usertypes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    private Collection $allowedUserTypes;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->subjects = new ArrayCollection();
        $this->allowedUserTypes = new ArrayCollection();
        $this->flags = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): StudentAbsenceType {
        $this->name = $name;
        return $this;
    }

    public function getDetails(): ?string {
        return $this->details;
    }

    public function setDetails(?string $details): StudentAbsenceType {
        $this->details = $details;
        return $this;
    }

    public function getBookLabel(): ?string {
        return $this->bookLabel;
    }

    public function setBookLabel(?string $bookLabel): StudentAbsenceType {
        $this->bookLabel = $bookLabel;
        return $this;
    }

    public function isMustApprove(): bool {
        return $this->mustApprove;
    }

    public function setMustApprove(bool $mustApprove): StudentAbsenceType {
        $this->mustApprove = $mustApprove;
        return $this;
    }

    public function isTypeWithZeroAbsenceLessons(): bool {
        return $this->isTypeWithZeroAbsenceLessons;
    }

    public function setIsTypeWithZeroAbsenceLessons(bool $isTypeWithZeroAbsenceLessons): StudentAbsenceType {
        $this->isTypeWithZeroAbsenceLessons = $isTypeWithZeroAbsenceLessons;
        return $this;
    }

    /**
     * @param AttendanceType $bookAttendanceType
     */
    public function setBookAttendanceType(AttendanceType $bookAttendanceType): void {
        $this->bookAttendanceType = $bookAttendanceType;
    }

    /**
     * @return AttendanceType
     */
    public function getBookAttendanceType(): AttendanceType {
        return $this->bookAttendanceType;
    }

    /**
     * @param AttendanceExcuseStatus $bookExcuseStatus
     */
    public function setBookExcuseStatus(AttendanceExcuseStatus $bookExcuseStatus): void {
        $this->bookExcuseStatus = $bookExcuseStatus;
    }

    /**
     * @return AttendanceExcuseStatus
     */
    public function getBookExcuseStatus(): AttendanceExcuseStatus {
        return $this->bookExcuseStatus;
    }

    public function addAllowedUserType(UserTypeEntity $entity): void {
        $this->allowedUserTypes->add($entity);
    }

    public function removeAllowedUserType(UserTypeEntity $entity): void {
        $this->allowedUserTypes->removeElement($entity);
    }

    public function getAllowedUserTypes(): Collection {
        return $this->allowedUserTypes;
    }

    public function addSubject(Subject $subject): void {
        $this->subjects->add($subject);
    }

    public function removeSubject(Subject $subject): void {
        $this->subjects->removeElement($subject);
    }

    public function getSubjects(): Collection {
        return $this->subjects;
    }

    public function isNotifySubjectTeacher(): bool {
        return $this->notifySubjectTeacher;
    }

    public function setNotifySubjectTeacher(bool $notifySubjectTeacher): StudentAbsenceType {
        $this->notifySubjectTeacher = $notifySubjectTeacher;
        return $this;
    }

    public function addFlag(AttendanceFlag $flag): void {
        $this->flags->add($flag);
    }

    public function removeFlag(AttendanceFlag $flag): void {
        $this->flags->removeElement($flag);
    }

    public function getFlags(): Collection {
        return $this->flags;
    }

    /**
     * @return string[]
     */
    public function getAdditionalRecipients(): array {
        return $this->additionalRecipients;
    }

    /**
     * @param string[] $additionalRecipients
     */
    public function setAdditionalRecipients(array $additionalRecipients): void {
        $this->additionalRecipients = $additionalRecipients;
    }

    public function __toString(): string {
        return $this->getName();
    }
}