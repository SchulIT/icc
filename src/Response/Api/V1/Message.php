<?php

namespace App\Response\Api\V1;

use App\Entity\UserTypeEntity;
use DateTime;
use App\Entity\Message as MessageEntity;
use App\Entity\MessageAttachment as MessageAttachmentEntity;
use App\Entity\StudyGroup as StudyGroupEntity;
use App\Entity\User as UserEntity;
use JMS\Serializer\Annotation as Serializer;

class Message {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("title")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $title;

    /**
     * @Serializer\SerializedName("title")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $content;

    /**
     * @Serializer\SerializedName("start_date")
     * @Serializer\Type("DateTime")
     *
     * @var DateTime
     */
    private $startDate;

    /**
     * @Serializer\SerializedName("expire_date")
     * @Serializer\Type("DateTime")
     *
     * @var DateTime
     */
    private $expireDate;

    /**
     * @Serializer\SerializedName("study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private $studyGroups;

    /**
     * @Serializer\SerializedName("attachments")
     * @Serializer\Type("array<App\Response\Api\V1\MessageAttachment>")
     *
     * @var MessageAttachment[]
     */
    private $attachments;

    /**
     * @Serializer\SerializedName("visibilities")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $visibilities;

    /**
     * @Serializer\SerializedName("scope")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $scope;

    /**
     * @Serializer\SerializedName("created_by")
     * @Serializer\Type("App\Response\Api\V1\User")
     *
     * @var User
     */
    private $createdBy;

    /**
     * @Serializer\SerializedName("created_at")
     * @Serializer\Type("DateTime")
     *
     * @var DateTime
     */
    private $createdAt;

    /**
     * @Serializer\SerializedName("updated_at")
     * @Serializer\Type("DateTime")
     *
     * @var DateTime|null
     */
    private $updatedAt;

    /**
     * @Serializer\SerializedName("downloads_enabled")
     * @Serializer\Type("bool")
     *
     * @var bool
     */
    private $isDownloadsEnabled;

    /**
     * @Serializer\SerializedName("download_user_types")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $downloadEnabledUserTypes;

    /**
     * @Serializer\SerializedName("download_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private $downloadEnabledStudyGroups;

    /**
     * @Serializer\SerializedName("uploads_enabled")
     * @Serializer\Type("bool")
     *
     * @var bool
     */
    private $isUploadEnabled;

    /**
     * @Serializer\SerializedName("upload_user_types")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $uploadEnabledUserTypes;

    /**
     * @Serializer\SerializedName("upload_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private $uploadEnabledStudyGroups;

    /**
     * @Serializer\SerializedName("upload_description")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $uploadDescription;

    /**
     * @Serializer\SerializedName("must_confirm")
     * @Serializer\Type("bool")
     *
     * @var bool
     */
    private $mustConfirm;

    /**
     * @Serializer\SerializedName("confirmation_user_types")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $confirmationRequiredUserTypes;

    /**
     * @Serializer\SerializedName("confirmation_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private $confirmationRequiredStudyGroups;

    /**
     * @Serializer\SerializedName("priority")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $priority;

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Message
     */
    public function setTitle(string $title): Message {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Message
     */
    public function setContent(string $content): Message {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     * @return Message
     */
    public function setStartDate(DateTime $startDate): Message {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpireDate(): DateTime {
        return $this->expireDate;
    }

    /**
     * @param DateTime $expireDate
     * @return Message
     */
    public function setExpireDate(DateTime $expireDate): Message {
        $this->expireDate = $expireDate;
        return $this;
    }

    /**
     * @return StudyGroup[]
     */
    public function getStudyGroups(): array {
        return $this->studyGroups;
    }

    /**
     * @param StudyGroup[] $studyGroups
     * @return Message
     */
    public function setStudyGroups(array $studyGroups): Message {
        $this->studyGroups = $studyGroups;
        return $this;
    }

    /**
     * @return MessageAttachment[]
     */
    public function getAttachments(): array {
        return $this->attachments;
    }

    /**
     * @param MessageAttachment[] $attachments
     * @return Message
     */
    public function setAttachments(array $attachments): Message {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getVisibilities(): array {
        return $this->visibilities;
    }

    /**
     * @param string[] $visibilities
     * @return Message
     */
    public function setVisibilities(array $visibilities): Message {
        $this->visibilities = $visibilities;
        return $this;
    }

    /**
     * @return string
     */
    public function getScope(): string {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return Message
     */
    public function setScope(string $scope): Message {
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
     * @param User $createdBy
     * @return Message
     */
    public function setCreatedBy(User $createdBy): Message {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Message
     */
    public function setCreatedAt(DateTime $createdAt): Message {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     * @return Message
     */
    public function setUpdatedAt(?DateTime $updatedAt): Message {
        $this->updatedAt = $updatedAt;
        return $this;
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
     * @return string[]
     */
    public function getDownloadEnabledUserTypes(): array {
        return $this->downloadEnabledUserTypes;
    }

    /**
     * @param string[] $downloadEnabledUserTypes
     * @return Message
     */
    public function setDownloadEnabledUserTypes(array $downloadEnabledUserTypes): Message {
        $this->downloadEnabledUserTypes = $downloadEnabledUserTypes;
        return $this;
    }

    /**
     * @return StudyGroup[]
     */
    public function getDownloadEnabledStudyGroups(): array {
        return $this->downloadEnabledStudyGroups;
    }

    /**
     * @param StudyGroup[] $downloadEnabledStudyGroups
     * @return Message
     */
    public function setDownloadEnabledStudyGroups(array $downloadEnabledStudyGroups): Message {
        $this->downloadEnabledStudyGroups = $downloadEnabledStudyGroups;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUploadEnabled(): bool {
        return $this->isUploadEnabled;
    }

    /**
     * @param bool $isUploadEnabled
     * @return Message
     */
    public function setIsUploadEnabled(bool $isUploadEnabled): Message {
        $this->isUploadEnabled = $isUploadEnabled;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getUploadEnabledUserTypes(): array {
        return $this->uploadEnabledUserTypes;
    }

    /**
     * @param string[] $uploadEnabledUserTypes
     * @return Message
     */
    public function setUploadEnabledUserTypes(array $uploadEnabledUserTypes): Message {
        $this->uploadEnabledUserTypes = $uploadEnabledUserTypes;
        return $this;
    }

    /**
     * @return StudyGroup[]
     */
    public function getUploadEnabledStudyGroups(): array {
        return $this->uploadEnabledStudyGroups;
    }

    /**
     * @param StudyGroup[] $uploadEnabledStudyGroups
     * @return Message
     */
    public function setUploadEnabledStudyGroups(array $uploadEnabledStudyGroups): Message {
        $this->uploadEnabledStudyGroups = $uploadEnabledStudyGroups;
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

    /**
     * @return bool
     */
    public function isMustConfirm(): bool {
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
     * @return string[]
     */
    public function getConfirmationRequiredUserTypes(): array {
        return $this->confirmationRequiredUserTypes;
    }

    /**
     * @param string[] $confirmationRequiredUserTypes
     * @return Message
     */
    public function setConfirmationRequiredUserTypes(array $confirmationRequiredUserTypes): Message {
        $this->confirmationRequiredUserTypes = $confirmationRequiredUserTypes;
        return $this;
    }

    /**
     * @return StudyGroup[]
     */
    public function getConfirmationRequiredStudyGroups(): array {
        return $this->confirmationRequiredStudyGroups;
    }

    /**
     * @param StudyGroup[] $confirmationRequiredStudyGroups
     * @return Message
     */
    public function setConfirmationRequiredStudyGroups(array $confirmationRequiredStudyGroups): Message {
        $this->confirmationRequiredStudyGroups = $confirmationRequiredStudyGroups;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriority(): string {
        return $this->priority;
    }

    /**
     * @param string $priority
     * @return Message
     */
    public function setPriority(string $priority): Message {
        $this->priority = $priority;
        return $this;
    }

    public static function fromEntity(MessageEntity $messageEntity): self {
        return (new self())
            ->setUuid($messageEntity->getUuid())
            ->setTitle($messageEntity->getTitle())
            ->setContent($messageEntity->getContent())
            ->setStartDate($messageEntity->getStartDate())
            ->setExpireDate($messageEntity->getExpireDate())
            ->setStudyGroups(array_map(function(StudyGroupEntity $studyGroupEntity) {
                return StudyGroup::fromEntity($studyGroupEntity);
            }, $messageEntity->getStudyGroups()->toArray()))
            ->setAttachments(array_map(function(MessageAttachmentEntity $attachmentEntity) {
                return MessageAttachment::fromEntity($attachmentEntity);
            }, $messageEntity->getAttachments()->toArray()))
            ->setVisibilities(array_map(function(UserTypeEntity $userTypeEntity) {
                return $userTypeEntity->getUserType()->getValue();
            }, $messageEntity->getVisibilities()->toArray()))
            ->setScope($messageEntity->getScope()->getValue())
            ->setCreatedBy(User::fromEntity($messageEntity->getCreatedBy()))
            ->setCreatedAt($messageEntity->getCreatedAt())
            ->setUpdatedAt($messageEntity->getUpdatedAt())
            ->setIsDownloadsEnabled($messageEntity->isDownloadsEnabled())
            ->setDownloadEnabledUserTypes(array_map(function(UserTypeEntity $userTypeEntity) {
                return $userTypeEntity->getUserType()->getValue();
            }, $messageEntity->getDownloadEnabledUserTypes()->toArray()))
            ->setDownloadEnabledStudyGroups(array_map(function(StudyGroupEntity $studyGroupEntity) {
                return StudyGroup::fromEntity($studyGroupEntity);
            }, $messageEntity->getDownloadEnabledStudyGroups()->toArray()))
            ->setIsUploadEnabled($messageEntity->isUploadsEnabled())
            ->setUploadEnabledUserTypes(array_map(function(UserTypeEntity $userTypeEntity) {
                return $userTypeEntity->getUserType()->getValue();
            }, $messageEntity->getUploadEnabledUserTypes()->toArray()))
            ->setUploadEnabledStudyGroups(array_map(function(StudyGroupEntity $studyGroupEntity) {
                return StudyGroup::fromEntity($studyGroupEntity);
            }, $messageEntity->getUploadEnabledStudyGroups()->toArray()))
            ->setUploadDescription($messageEntity->getUploadDescription())
            ->setMustConfirm($messageEntity->mustConfirm())
            ->setConfirmationRequiredUserTypes(array_map(function(UserTypeEntity $userTypeEntity) {
                return $userTypeEntity->getUserType()->getValue();
            }, $messageEntity->getConfirmationRequiredUserTypes()->toArray()))
            ->setConfirmationRequiredStudyGroups(array_map(function(StudyGroupEntity $studyGroupEntity) {
                return StudyGroup::fromEntity($studyGroupEntity);
            }, $messageEntity->getConfirmationRequiredStudyGroups()->toArray()))
            ->setPriority($messageEntity->getPriority()->getValue());

    }
}