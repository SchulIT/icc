<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"username"})
 */
class User implements UserInterface {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

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
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
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
     * @ORM\Column(type="UserType::class")
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

    public function __construct() {
        $this->students = new ArrayCollection();
        $this->dismissedMessages = new ArrayCollection();
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
}