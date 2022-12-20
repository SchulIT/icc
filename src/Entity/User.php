<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['username'])]
#[ORM\Entity]
class User implements UserInterface, Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'uuid')]
    private ?UuidInterface $idpId = null;

    #[ORM\Column(type: 'string', unique: true)]
    private ?string $username = null;

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firstname = null;

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lastname = null;

    #[Assert\Email]
    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Teacher $teacher = null;

    /**
     * @var Collection<Student>
     */
    #[ORM\JoinTable(name: 'user_students')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Student::class)]
    private $students;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type: 'string', enumType: UserType::class)]
    private ?UserType $userType = null;

    /**
     * @var ArrayCollection<Message>
     */
    #[ORM\JoinTable(name: 'user_dismissed_messages')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Message::class)]
    private $dismissedMessages;

    #[ORM\Column(type: 'boolean')]
    private bool $isSubstitutionNotificationsEnabled = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isExamNotificationsEnabled = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isMessageNotificationsEnabled = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isEmailNotificationsEnabled = false;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $data = [ ];

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->students = new ArrayCollection();
        $this->dismissedMessages = new ArrayCollection();
    }

    public function getIdpId(): ?UuidInterface {
        return $this->idpId;
    }

    public function setIdpId(UuidInterface $uuid): User {
        $this->idpId = $uuid;
        return $this;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): User {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): User {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): User {
        $this->email = $email;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): User {
        $this->teacher = $teacher;
        return $this;
    }

    public function getUserType(): UserType {
        return $this->userType;
    }

    public function setUserType(UserType $userType): User {
        $this->userType = $userType;
        return $this;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles) {
        $this->roles = $roles;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array {
        return $this->roles;
    }

    public function setUsername(string $username): User {
        $this->username = $username;
        return $this;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function addDismissedMessage(Message $message) {
        $this->dismissedMessages->add($message);
    }

    public function removeDismissedMessage(Message $message) {
        $this->dismissedMessages->removeElement($message);
    }

    /**
     * @return Collection<Message>
     */
    public function getDismissedMessages(): Collection {
        return $this->dismissedMessages;
    }

    public function addStudent(Student $student) {
        $this->students->add($student);
    }

    public function removeStudent(Student $student) {
        $this->students->removeElement($student);
    }

    /**
     * @return Collection<Student>
     */
    public function getStudents(): Collection {
        return $this->students;
    }

    public function isSubstitutionNotificationsEnabled(): bool {
        return $this->isSubstitutionNotificationsEnabled;
    }

    public function setIsSubstitutionNotificationsEnabled(bool $isSubstitutionNotificationsEnabled): User {
        $this->isSubstitutionNotificationsEnabled = $isSubstitutionNotificationsEnabled;
        return $this;
    }

    public function isExamNotificationsEnabled(): bool {
        return $this->isExamNotificationsEnabled;
    }

    public function setIsExamNotificationsEnabled(bool $isExamNotificationsEnabled): User {
        $this->isExamNotificationsEnabled = $isExamNotificationsEnabled;
        return $this;
    }

    public function isMessageNotificationsEnabled(): bool {
        return $this->isMessageNotificationsEnabled;
    }

    public function setIsMessageNotificationsEnabled(bool $isMessageNotificationsEnabled): User {
        $this->isMessageNotificationsEnabled = $isMessageNotificationsEnabled;
        return $this;
    }

    public function isEmailNotificationsEnabled(): bool {
        return $this->isEmailNotificationsEnabled;
    }

    public function setIsEmailNotificationsEnabled(bool $isEmailNotificationsEnabled): User {
        $this->isEmailNotificationsEnabled = $isEmailNotificationsEnabled;
        return $this;
    }

    public function getData(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }

    public function setData(string $key, $data): void {
        $this->data[$key] = $data;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }

    public function getUserIdentifier(): string {
        return $this->getUsername();
    }

    public function __serialize(): array {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername()
        ];
    }

    public function __unserialize(array $serialized) {
        $this->id = $serialized['id'];
        $this->username = $serialized['username'];
    }

    public function __toString(): string {
        return sprintf('%s, %s (%s)', $this->getLastname(), $this->getFirstname(), $this->getUsername());
    }

    public function isStudent(): bool {
        return $this->getUserType() === UserType::Student;
    }

    public function isParent(): bool {
        return $this->getUserType() === UserType::Parent;
    }

    public function isStudentOrParent(): bool {
        return $this->isStudent() || $this->isParent();
    }

    public function isTeacher(): bool {
        return $this->getUserType() === UserType::Teacher;
    }
}