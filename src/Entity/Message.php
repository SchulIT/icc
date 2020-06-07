<?php

namespace App\Entity;

use App\Validator\SubsetOf;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Message {

   use IdTrait;
   use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", name="start_date")
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", name="expire_date")
     * @Assert\GreaterThan(propertyPath="startDate")
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $expireDate;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="message_studygroups",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @var Collection<StudyGroup>
     */
    private $studyGroups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageAttachment", mappedBy="message", cascade={"persist"})
     * @var ArrayCollection<MessageAttachment>
     */
    private $attachments;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="message_visibilities",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<UserTypeEntity>
     */
    private $visibilities;

    /**
     * @ORM\Column(type="message_scope")
     * @var MessageScope
     */
    private $scope;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Gedmo\Blameable(on="create")
     * @var User
     */
    private $createdBy = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isDownloadsEnabled = false;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="message_download_enabled_usertypes",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @SubsetOf(propertyPath="visibilities")
     * @var Collection<UserTypeEntity>
     */
    private $downloadEnabledUserTypes;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="message_download_enabled_studygroups",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @SubsetOf(propertyPath="studyGroups")
     * @var Collection<StudyGroup>
     */
    private $downloadEnabledStudyGroups;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isUploadsEnabled = false;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="message_upload_enabled_usertypes",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @SubsetOf(propertyPath="visibilities")
     * @var Collection<UserTypeEntity>
     */
    private $uploadEnabledUserTypes;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="message_upload_enabled_studygroups",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @SubsetOf(propertyPath="studyGroups")
     * @var Collection<StudyGroup>
     */
    private $uploadEnabledStudyGroups;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $uploadDescription;

    /**
     * @ORM\OneToMany(targetEntity="MessageFile", mappedBy="message", cascade={"persist"})
     * @Assert\Valid()
     * @var Collection<MessageFile>
     */
    private $files;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $hiddenFromDashboard = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isEmailNotificationSent = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isPushNotificationSent = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $mustConfirm = false;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="message_confirmation_required_usertypes",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @SubsetOf(propertyPath="visibilities")
     * @var Collection<UserTypeEntity>
     */
    private $confirmationRequiredUserTypes;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="message_confirmation_required_studygroups",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @SubsetOf(propertyPath="studyGroups")
     * @var Collection<StudyGroup>
     */
    private $confirmationRequiredStudyGroups;

    /**
     * @ORM\OneToMany(targetEntity="MessageConfirmation", mappedBy="message")
     * @var Collection<MessageConfirmation>
     */
    private $confirmations;

    /**
     * @ORM\Column(type="message_priority")
     * @var MessagePriority
     */
    private $priority;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->studyGroups = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->visibilities = new ArrayCollection();
        $this->confirmations = new ArrayCollection();
        $this->confirmationRequiredStudyGroups = new ArrayCollection();
        $this->confirmationRequiredUserTypes = new ArrayCollection();
        $this->uploadEnabledStudyGroups = new ArrayCollection();
        $this->uploadEnabledUserTypes = new ArrayCollection();
        $this->downloadEnabledStudyGroups = new ArrayCollection();
        $this->downloadEnabledUserTypes = new ArrayCollection();

        $this->scope = MessageScope::Messages();
        $this->priority = MessagePriority::Normal();
    }

    /**
     * @return string
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Message
     */
    public function setTitle(?string $title): Message {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Message
     */
    public function setContent(?string $content): Message {
        $this->content = $content;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): ?\DateTime {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return Message
     */
    public function setStartDate(\DateTime $startDate): Message {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpireDate(): ?\DateTime {
        return $this->expireDate;
    }

    /**
     * @param \DateTime $expireDate
     * @return Message
     */
    public function setExpireDate(\DateTime $expireDate): Message {
        $this->expireDate = $expireDate;
        return $this;
    }

    public function addStudyGroup(StudyGroup $studyGroup) {
        $this->studyGroups->add($studyGroup);
    }

    public function removeStudyGroups(StudyGroup $studyGroup) {
        $this->studyGroups->removeElement($studyGroup);
    }

    /**
     * @return Collection<StudyGroup>
     */
    public function getStudyGroups(): Collection {
        return $this->studyGroups;
    }

    public function addAttachment(MessageAttachment $attachment) {
        if($attachment->getMessage() === $this) {
            // Do not readd already existing attachments (seems to fix a bug with VichUploaderBundle https://github.com/dustin10/VichUploaderBundle/issues/842)
            return;
        }

        $attachment->setMessage($this); // important as MessageFilesystem needs $attachment->getMessage()
        $this->attachments->add($attachment);
    }

    public function removeAttachment(MessageAttachment $attachment) {
        $this->attachments->removeElement($attachment);
    }

    /**
     * @return Collection<MessageAttachment>
     */
    public function getAttachments(): Collection {
        return $this->attachments;
    }

    public function addVisibility(UserTypeEntity $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(UserTypeEntity $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return Collection<UserTypeEntity>
     */
    public function getVisibilities(): Collection {
        return $this->visibilities;
    }

    /**
     * @return MessageScope
     */
    public function getScope(): MessageScope {
        return $this->scope;
    }

    /**
     * @param MessageScope $scope
     * @return Message
     */
    public function setScope(MessageScope $scope): Message {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    /**
     * @return bool
     */
    public function isDownloadsEnabled(): bool {
        return $this->isDownloadsEnabled;
    }

    /**
     * @param bool $isDownloadsEnabled
     * @return Message
     */
    public function setIsDownloadsEnabled(bool $isDownloadsEnabled): Message {
        $this->isDownloadsEnabled = $isDownloadsEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUploadsEnabled(): bool {
        return $this->isUploadsEnabled;
    }

    /**
     * @param bool $isUploadsEnabled
     * @return Message
     */
    public function setIsUploadsEnabled(bool $isUploadsEnabled): Message {
        $this->isUploadsEnabled = $isUploadsEnabled;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUploadDescription(): ?string {
        return $this->uploadDescription;
    }

    /**
     * @param string|null $uploadDescription
     * @return Message
     */
    public function setUploadDescription(?string $uploadDescription): Message {
        $this->uploadDescription = $uploadDescription;
        return $this;
    }

    public function addFile(MessageFile $file) {
        $file->setMessage($this);
        $this->files->add($file);
    }

    public function removeFile(MessageFile $file) {
        $this->files->removeElement($file);
    }

    /**
     * @return Collection<MessageFile>
     */
    public function getFiles(): Collection {
        return $this->files;
    }

    /**
     * @return bool
     */
    public function isHiddenFromDashboard(): bool {
        return $this->hiddenFromDashboard;
    }

    /**
     * @param bool $hiddenFromDashboard
     * @return Message
     */
    public function setHiddenFromDashboard(bool $hiddenFromDashboard): Message {
        $this->hiddenFromDashboard = $hiddenFromDashboard;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailNotificationSent(): bool {
        return $this->isEmailNotificationSent;
    }

    /**
     * @param bool $isEmailNotificationSent
     * @return Message
     */
    public function setIsEmailNotificationSent(bool $isEmailNotificationSent): Message {
        $this->isEmailNotificationSent = $isEmailNotificationSent;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPushNotificationSent(): bool {
        return $this->isPushNotificationSent;
    }

    /**
     * @param bool $isPushNotificationSent
     * @return Message
     */
    public function setIsPushNotificationSent(bool $isPushNotificationSent): Message {
        $this->isPushNotificationSent = $isPushNotificationSent;
        return $this;
    }

    /**
     * @return bool
     */
    public function mustConfirm(): bool {
        return $this->mustConfirm;
    }

    /**
     * @param bool $mustConfirm
     * @return Message
     */
    public function setMustConfirm(bool $mustConfirm): Message {
        $this->mustConfirm = $mustConfirm;
        return $this;
    }

    /**
     * @return Collection<MessageConfirmation>
     */
    public function getConfirmations(): Collection {
        return $this->confirmations;
    }

    /**
     * @return Collection
     */
    public function getUploadEnabledUserTypes(): Collection {
        return $this->uploadEnabledUserTypes;
    }

    /**
     * @return Collection
     */
    public function getDownloadEnabledUserTypes(): Collection {
        return $this->downloadEnabledUserTypes;
    }

    /**
     * @return Collection
     */
    public function getDownloadEnabledStudyGroups(): Collection {
        return $this->downloadEnabledStudyGroups;
    }

    /**
     * @return Collection
     */
    public function getUploadEnabledStudyGroups(): Collection {
        return $this->uploadEnabledStudyGroups;
    }

    /**
     * @return Collection
     */
    public function getConfirmationRequiredUserTypes(): Collection {
        return $this->confirmationRequiredUserTypes;
    }

    /**
     * @return Collection
     */
    public function getConfirmationRequiredStudyGroups(): Collection {
        return $this->confirmationRequiredStudyGroups;
    }

    /**
     * @return MessagePriority
     */
    public function getPriority(): MessagePriority {
        return $this->priority;
    }

    /**
     * @param MessagePriority $priority
     * @return Message
     */
    public function setPriority(MessagePriority $priority): Message {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime {
        return $this->updatedAt;
    }

    public function __toString() {
        return $this->getTitle();
    }
}