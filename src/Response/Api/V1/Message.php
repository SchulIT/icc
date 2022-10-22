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
     */
    private ?string $title = null;

    /**
     * @Serializer\SerializedName("title")
     * @Serializer\Type("string")
     */
    private ?string $content = null;

    /**
     * @Serializer\SerializedName("start_date")
     * @Serializer\Type("DateTime")
     */
    private ?\DateTime $startDate = null;

    /**
     * @Serializer\SerializedName("expire_date")
     * @Serializer\Type("DateTime")
     */
    private ?\DateTime $expireDate = null;

    /**
     * @Serializer\SerializedName("study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private ?array $studyGroups = null;

    /**
     * @Serializer\SerializedName("attachments")
     * @Serializer\Type("array<App\Response\Api\V1\MessageAttachment>")
     *
     * @var MessageAttachment[]
     */
    private ?array $attachments = null;

    /**
     * @Serializer\SerializedName("visibilities")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private ?array $visibilities = null;

    /**
     * @Serializer\SerializedName("scope")
     * @Serializer\Type("string")
     */
    private ?string $scope = null;

    /**
     * @Serializer\SerializedName("created_by")
     * @Serializer\Type("App\Response\Api\V1\User")
     */
    private ?\App\Response\Api\V1\User $createdBy = null;

    /**
     * @Serializer\SerializedName("created_at")
     * @Serializer\Type("DateTime")
     */
    private ?\DateTime $createdAt = null;

    /**
     * @Serializer\SerializedName("updated_at")
     * @Serializer\Type("DateTime")
     */
    private ?\DateTime $updatedAt = null;

    /**
     * @Serializer\SerializedName("downloads_enabled")
     * @Serializer\Type("bool")
     */
    private ?bool $isDownloadsEnabled = null;

    /**
     * @Serializer\SerializedName("download_user_types")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private ?array $downloadEnabledUserTypes = null;

    /**
     * @Serializer\SerializedName("download_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private ?array $downloadEnabledStudyGroups = null;

    /**
     * @Serializer\SerializedName("uploads_enabled")
     * @Serializer\Type("bool")
     */
    private ?bool $isUploadEnabled = null;

    /**
     * @Serializer\SerializedName("upload_user_types")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private ?array $uploadEnabledUserTypes = null;

    /**
     * @Serializer\SerializedName("upload_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private ?array $uploadEnabledStudyGroups = null;

    /**
     * @Serializer\SerializedName("upload_description")
     * @Serializer\Type("string")
     */
    private ?string $uploadDescription = null;

    /**
     * @Serializer\SerializedName("must_confirm")
     * @Serializer\Type("bool")
     */
    private ?bool $mustConfirm = null;

    /**
     * @Serializer\SerializedName("confirmation_user_types")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private ?array $confirmationRequiredUserTypes = null;

    /**
     * @Serializer\SerializedName("confirmation_study_groups")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroup>")
     *
     * @var StudyGroup[]
     */
    private ?array $confirmationRequiredStudyGroups = null;

    /**
     * @Serializer\SerializedName("priority")
     * @Serializer\Type("string")
     */
    private ?string $priority = null;

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): Message {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): Message {
        $this->content = $content;
        return $this;
    }

    public function getStartDate(): DateTime {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): Message {
        $this->startDate = $startDate;
        return $this;
    }

    public function getExpireDate(): DateTime {
        return $this->expireDate;
    }

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
     */
    public function setVisibilities(array $visibilities): Message {
        $this->visibilities = $visibilities;
        return $this;
    }

    public function getScope(): string {
        return $this->scope;
    }

    public function setScope(string $scope): Message {
        $this->scope = $scope;
        return $this;
    }

    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): Message {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Message {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): Message {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isDownloadsEnabled(): bool {
        return $this->isDownloadsEnabled;
    }

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
     */
    public function setDownloadEnabledStudyGroups(array $downloadEnabledStudyGroups): Message {
        $this->downloadEnabledStudyGroups = $downloadEnabledStudyGroups;
        return $this;
    }

    public function isUploadEnabled(): bool {
        return $this->isUploadEnabled;
    }

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
     */
    public function setUploadEnabledStudyGroups(array $uploadEnabledStudyGroups): Message {
        $this->uploadEnabledStudyGroups = $uploadEnabledStudyGroups;
        return $this;
    }

    public function getUploadDescription(): ?string {
        return $this->uploadDescription;
    }

    public function setUploadDescription(?string $uploadDescription): Message {
        $this->uploadDescription = $uploadDescription;
        return $this;
    }

    public function isMustConfirm(): bool {
        return $this->mustConfirm;
    }

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
     */
    public function setConfirmationRequiredStudyGroups(array $confirmationRequiredStudyGroups): Message {
        $this->confirmationRequiredStudyGroups = $confirmationRequiredStudyGroups;
        return $this;
    }

    public function getPriority(): string {
        return $this->priority;
    }

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
            ->setStudyGroups(array_map(fn(StudyGroupEntity $studyGroupEntity) => StudyGroup::fromEntity($studyGroupEntity), $messageEntity->getStudyGroups()->toArray()))
            ->setAttachments(array_map(fn(MessageAttachmentEntity $attachmentEntity) => MessageAttachment::fromEntity($attachmentEntity), $messageEntity->getAttachments()->toArray()))
            ->setVisibilities(array_map(fn(UserTypeEntity $userTypeEntity) => $userTypeEntity->getUserType()->getValue(), $messageEntity->getVisibilities()->toArray()))
            ->setScope($messageEntity->getScope()->getValue())
            ->setCreatedBy(User::fromEntity($messageEntity->getCreatedBy()))
            ->setCreatedAt($messageEntity->getCreatedAt())
            ->setUpdatedAt($messageEntity->getUpdatedAt())
            ->setIsDownloadsEnabled($messageEntity->isDownloadsEnabled())
            ->setDownloadEnabledUserTypes(array_map(fn(UserTypeEntity $userTypeEntity) => $userTypeEntity->getUserType()->getValue(), $messageEntity->getDownloadEnabledUserTypes()->toArray()))
            ->setDownloadEnabledStudyGroups(array_map(fn(StudyGroupEntity $studyGroupEntity) => StudyGroup::fromEntity($studyGroupEntity), $messageEntity->getDownloadEnabledStudyGroups()->toArray()))
            ->setIsUploadEnabled($messageEntity->isUploadsEnabled())
            ->setUploadEnabledUserTypes(array_map(fn(UserTypeEntity $userTypeEntity) => $userTypeEntity->getUserType()->getValue(), $messageEntity->getUploadEnabledUserTypes()->toArray()))
            ->setUploadEnabledStudyGroups(array_map(fn(StudyGroupEntity $studyGroupEntity) => StudyGroup::fromEntity($studyGroupEntity), $messageEntity->getUploadEnabledStudyGroups()->toArray()))
            ->setUploadDescription($messageEntity->getUploadDescription())
            ->setMustConfirm($messageEntity->mustConfirm())
            ->setConfirmationRequiredUserTypes(array_map(fn(UserTypeEntity $userTypeEntity) => $userTypeEntity->getUserType()->getValue(), $messageEntity->getConfirmationRequiredUserTypes()->toArray()))
            ->setConfirmationRequiredStudyGroups(array_map(fn(StudyGroupEntity $studyGroupEntity) => StudyGroup::fromEntity($studyGroupEntity), $messageEntity->getConfirmationRequiredStudyGroups()->toArray()))
            ->setPriority($messageEntity->getPriority()->getValue());

    }
}