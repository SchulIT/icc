<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"username"})
 */
class User implements UserInterface, \Serializable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="uuid")
     * @var UuidInterface
     */
    private $idpId;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @var Teacher|null
     */
    private $teacher = null;

    /**
     * @ORM\ManyToMany(targetEntity="Student")
     * @ORM\JoinTable(name="user_students",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE", onDelete="CASCADE")}
     * )
     * @var Collection<Student>
     */
    private $students;

    /**
     * @ORM\Column(type="json_array")
     * @var string[]
     */
    private $roles = ['ROLE_USER'];

    /**
     * @ORM\Column(type="user_type")
     * @var UserType
     */
    private $userType;

    /**
     * @ORM\ManyToMany(targetEntity="Message")
     * @ORM\JoinTable(name="user_dismissed_messages",
     *     joinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="message", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @var ArrayCollection<Message>
     */
    private $dismissedMessages;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isSubstitutionNotificationsEnabled = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isExamNotificationsEnabled = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isMessageNotificationsEnabled = false;

    /**
     * @ORM\Column(type="json_array")
     * @var string[]
     */
    private $data = [ ];

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->students = new ArrayCollection();
        $this->dismissedMessages = new ArrayCollection();
    }

    /**
     * @return UuidInterface|null
     */
    public function getIdpId(): ?UuidInterface {
        return $this->idpId;
    }

    /**
     * @param UuidInterface $uuid
     * @return User
     */
    public function setIdpId(UuidInterface $uuid): User {
        $this->idpId = $uuid;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return User
     */
    public function setFirstname(string $firstname): User {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return User
     */
    public function setLastname(string $lastname): User {
        $this->lastname = $lastname;
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
     * @return User
     */
    public function setEmail(?string $email): User {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return User
     */
    public function setTeacher(?Teacher $teacher): User {
        $this->teacher = $teacher;
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
     * @return User
     */
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
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername() {
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

    /**
     * @return bool
     */
    public function isSubstitutionNotificationsEnabled(): bool {
        return $this->isSubstitutionNotificationsEnabled;
    }

    /**
     * @param bool $isSubstitutionNotificationsEnabled
     * @return User
     */
    public function setIsSubstitutionNotificationsEnabled(bool $isSubstitutionNotificationsEnabled): User {
        $this->isSubstitutionNotificationsEnabled = $isSubstitutionNotificationsEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExamNotificationsEnabled(): bool {
        return $this->isExamNotificationsEnabled;
    }

    /**
     * @param bool $isExamNotificationsEnabled
     * @return User
     */
    public function setIsExamNotificationsEnabled(bool $isExamNotificationsEnabled): User {
        $this->isExamNotificationsEnabled = $isExamNotificationsEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMessageNotificationsEnabled(): bool {
        return $this->isMessageNotificationsEnabled;
    }

    /**
     * @param bool $isMessageNotificationsEnabled
     * @return User
     */
    public function setIsMessageNotificationsEnabled(bool $isMessageNotificationsEnabled): User {
        $this->isMessageNotificationsEnabled = $isMessageNotificationsEnabled;
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
    public function getPassword() {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }

    /**
     * @inheritDoc
     */
    public function serialize() {
        return serialize([
            $this->getId(),
            $this->getUsername()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized) {
        list($this->id, $this->username) = unserialize($serialized);
    }
}