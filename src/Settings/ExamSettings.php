<?php

namespace App\Settings;

use App\Entity\UserType;

class ExamSettings extends AbstractSettings {

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function getVisibility() {
        return $this->getValue('exams.visibility', [ ]);
    }

    public function setVisibility(array $visibility) {
        $this->setValue('exams.visibility', $visibility);
    }

    public function isVisibileFor(UserType $type) {
        foreach($this->getVisibility() as $visibleUserType) {
            if($type->equals($visibleUserType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the number of days students can view in the future. Detaults to 0 (unlimited days)
     *
     * @return int
     */
    public function getTimeWindowForStudents(): int {
        return (int)$this->getValue('exams.window', 0);
    }

    public function setTimeWindowForStudents(int $timeWindow) {
        $this->setValue('exams.window', $timeWindow);
    }

    /**
     * Returns the number of days students can view invigilators for future exams. Detaults to 0 (unlimited days)
     *
     * @return int
     */
    public function getTimeWindowForStudentsToSeeInvigilators(): int {
        return (int)$this->getValue('exams.window.invigilators', 0);
    }

    public function setTimeWindowForStudentsToSeeInvigilators(int $timeWindow) {
        $this->setValue('exams.window.invigilators', $timeWindow);
    }

    public function isNotificationsEnabled(): bool {
        return (bool)$this->getValue('exams.notifications.enabled', false);
    }

    public function setNotificationsEnabled(bool $enabled): void {
        $this->setValue('exams.notifications.enabled', $enabled);
    }

    public function getNotificationReplyToAddress(): ?string {
        return (string)$this->getValue('exams.notifications.reply_to', null);
    }

    public function setNotificationReplyToAddress(?string $address): void {
        $this->setValue('exams.notifications.reply_to', $address);
    }

    public function getNotificationSender(): ?string {
        return (string)$this->getValue('exams.notifications.sender', null);
    }

    public function setNotificationSender(?string $sender): void {
        $this->setValue('exams.notifications.sender', $sender);
    }

    public function getMaximumNumberOfExamsPerWeek(): int {
        return (int)$this->getValue('exams.max_per_week', 2);
    }

    public function setMaximumNumberOfExamsPerWeek(int $number): void {
        $this->setValue('exams.max_per_week', $number);
    }

    public function getMaximumNumberOfExamsPerDay(): int {
        return (int)$this->getValue('exams.max_per_day', 1);
    }

    public function setMaximumNumberOfExamsPerDay(int $number): void {
        $this->setValue('exams.max_per_day', $number);
    }
}