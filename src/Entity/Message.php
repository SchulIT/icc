<?php

namespace App\Entity;

use Stringable;
use App\Validator\CollectionNotEmpty;
use App\Validator\SubsetOf;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
#[ORM\Index(columns: ['title'], flags: ['fulltext'])]
#[ORM\Index(columns: ['content'], flags: ['fulltext'])]
class Message implements Stringable {

   use IdTrait;
   use UuidTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $startDate = null;

    #[Assert\GreaterThan(propertyPath: 'startDate')]
    #[Assert\NotNull]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $expireDate = null;

    /**
     * @var Collection<StudyGroup>
     */
    #[ORM\JoinTable(name: 'message_studygroups')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: StudyGroup::class)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[CollectionNotEmpty(propertyPath: 'visibilities')]
    private $studyGroups;

    /**
     * @var ArrayCollection<MessageAttachment>
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageAttachment::class, cascade: ['persist'])]
    #[ORM\OrderBy(['filename' => 'asc'])]
    private $attachments;

    /**
     * @var Collection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'message_visibilities')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    private $visibilities;

    #[ORM\Column(type: 'string', enumType: MessageScope::class)]
    private MessageScope $scope;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $updatedAt;

    #[Gedmo\Blameable(on: 'update')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isDownloadsEnabled = false;

    /**
     * @var Collection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'message_download_usertypes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    #[SubsetOf(propertyPath: 'visibilities')]
    private $downloadEnabledUserTypes;

    /**
     * @var Collection<StudyGroup>
     */
    #[ORM\JoinTable(name: 'message_download_studygroups')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: StudyGroup::class)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[SubsetOf(propertyPath: 'studyGroups')]
    private $downloadEnabledStudyGroups;

    #[ORM\Column(type: 'boolean')]
    private bool $isUploadsEnabled = false;

    /**
     * @var Collection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'message_upload_usertypes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    #[SubsetOf(propertyPath: 'visibilities')]
    private $uploadEnabledUserTypes;

    /**
     * @var Collection<StudyGroup>
     */
    #[ORM\JoinTable(name: 'message_upload_studygroups')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: StudyGroup::class)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[SubsetOf(propertyPath: 'studyGroups')]
    private $uploadEnabledStudyGroups;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $uploadDescription = null;

    /**
     * @var Collection<MessageFile>
     */
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageFile::class, cascade: ['persist'])]
    private $files;

    #[ORM\Column(type: 'boolean')]
    private bool $isEmailNotificationSent = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isPushNotificationSent = false;

    #[ORM\Column(type: 'boolean')]
    private bool $mustConfirm = false;

    /**
     * @var Collection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'message_confirmation_usertypes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    #[SubsetOf(propertyPath: 'visibilities')]
    private $confirmationRequiredUserTypes;

    /**
     * @var Collection<StudyGroup>
     */
    #[ORM\JoinTable(name: 'message_confirmation_studygroups')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: StudyGroup::class)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[SubsetOf(propertyPath: 'studyGroups')]
    private $confirmationRequiredStudyGroups;

    /**
     * @var Collection<MessageConfirmation>
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageConfirmation::class)]
    private $confirmations;

    #[ORM\Column(type: 'string', enumType: MessagePriority::class)]
    private MessagePriority $priority;

    #[ORM\Column(type: 'boolean')]
    private bool $isPollEnabled = false;

    #[ORM\Column(type: 'boolean')]
    private bool $allowPollRevote = true;

    #[Assert\GreaterThanOrEqual(1)]
    #[ORM\Column(type: 'integer')]
    private int $pollNumChoices = 1;

    /**
     * @var Collection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'message_poll_usertypes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    #[SubsetOf(propertyPath: 'visibilities')]
    private $pollUserTypes;

    /**
     * @var Collection<StudyGroup>
     */
    #[ORM\JoinTable(name: 'message_poll_studygroups')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: StudyGroup::class)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[SubsetOf(propertyPath: 'studyGroups')]
    private $pollStudyGroups;

    /**
     * @var Collection<MessagePollChoice>
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessagePollChoice::class, cascade: ['persist'], orphanRemoval: true)]
    private $pollChoices;

    /**
     * @var Collection<MessagePollVote>
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessagePollVote::class)]
    private $pollVotes;

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
        $this->pollStudyGroups = new ArrayCollection();
        $this->pollUserTypes = new ArrayCollection();
        $this->pollChoices = new ArrayCollection();
        $this->pollVotes = new ArrayCollection();

        $this->scope = MessageScope::Messages;
        $this->priority = MessagePriority::Normal;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string $title
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
     */
    public function setContent(?string $content): Message {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): ?DateTime {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): Message {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpireDate(): ?DateTime {
        return $this->expireDate;
    }

    public function setExpireDate(DateTime $expireDate): Message {
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

    public function getScope(): MessageScope {
        return $this->scope;
    }

    public function setScope(MessageScope $scope): Message {
        $this->scope = $scope;
        return $this;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): Message {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function isDownloadsEnabled(): bool {
        return $this->isDownloadsEnabled;
    }

    public function setIsDownloadsEnabled(bool $isDownloadsEnabled): Message {
        $this->isDownloadsEnabled = $isDownloadsEnabled;
        return $this;
    }

    public function isUploadsEnabled(): bool {
        return $this->isUploadsEnabled;
    }

    public function setIsUploadsEnabled(bool $isUploadsEnabled): Message {
        $this->isUploadsEnabled = $isUploadsEnabled;
        return $this;
    }

    public function getUploadDescription(): ?string {
        return $this->uploadDescription;
    }

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

    public function isEmailNotificationSent(): bool {
        return $this->isEmailNotificationSent;
    }

    public function setIsEmailNotificationSent(bool $isEmailNotificationSent): Message {
        $this->isEmailNotificationSent = $isEmailNotificationSent;
        return $this;
    }

    public function isPushNotificationSent(): bool {
        return $this->isPushNotificationSent;
    }

    public function setIsPushNotificationSent(bool $isPushNotificationSent): Message {
        $this->isPushNotificationSent = $isPushNotificationSent;
        return $this;
    }

    public function mustConfirm(): bool {
        return $this->mustConfirm;
    }

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

    public function getUploadEnabledUserTypes(): Collection {
        return $this->uploadEnabledUserTypes;
    }

    public function getDownloadEnabledUserTypes(): Collection {
        return $this->downloadEnabledUserTypes;
    }

    public function getDownloadEnabledStudyGroups(): Collection {
        return $this->downloadEnabledStudyGroups;
    }

    public function getUploadEnabledStudyGroups(): Collection {
        return $this->uploadEnabledStudyGroups;
    }

    /**
     * @return Collection<UserTypeEntity>
     */
    public function getConfirmationRequiredUserTypes(): Collection {
        return $this->confirmationRequiredUserTypes;
    }

    public function getConfirmationRequiredStudyGroups(): Collection {
        return $this->confirmationRequiredStudyGroups;
    }

    public function getPriority(): MessagePriority {
        return $this->priority;
    }

    public function setPriority(MessagePriority $priority): Message {
        $this->priority = $priority;
        return $this;
    }

    public function isPollEnabled(): bool {
        return $this->isPollEnabled;
    }

    public function setIsPollEnabled(bool $isPollEnabled): Message {
        $this->isPollEnabled = $isPollEnabled;
        return $this;
    }

    public function isAllowPollRevote(): bool {
        return $this->allowPollRevote;
    }

    public function setAllowPollRevote(bool $allowPollRevote): Message {
        $this->allowPollRevote = $allowPollRevote;
        return $this;
    }

    public function getPollNumChoices(): int {
        return $this->pollNumChoices;
    }

    public function setPollNumChoices(int $pollNumChoices): Message {
        $this->pollNumChoices = $pollNumChoices;
        return $this;
    }

    public function getPollUserTypes(): Collection {
        return $this->pollUserTypes;
    }

    public function getPollStudyGroups(): Collection {
        return $this->pollStudyGroups;
    }

    /**
     * @return Collection<MessagePollChoice>
     */
    public function getPollChoices(): Collection {
        return $this->pollChoices;
    }

    public function addPollChoice(MessagePollChoice $choice): void {
        $choice->setMessage($this);
        $this->pollChoices->add($choice);
    }

    public function removePollChoice(MessagePollChoice $choice): void {
        $this->pollChoices->removeElement($choice);
    }

    public function addPollVote(MessagePollVote $vote): void {
        $this->pollVotes->add($vote);
    }

    /**
     * @return Collection<MessagePollVote>
     */
    public function getPollVotes(): Collection {
        return $this->pollVotes;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        if($this->updatedAt === null) {
            return $this->getCreatedAt();
        }

        return $this->updatedAt;
    }

    public function getUpdatedBy(): ?User {
        if($this->updatedBy === null) {
            return $this->getCreatedBy();
        }

        return $this->updatedBy;
    }

    public function __toString(): string {
        return (string) $this->getTitle();
    }
}