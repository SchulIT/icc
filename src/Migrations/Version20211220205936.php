<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211220205936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_category_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE book_comment_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE display_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_attachment_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_category_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_supervision_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE excuse_note_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE free_timespan_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE grade_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE grade_membership_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE grade_teacher_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE infotext_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE lesson_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE lesson_attendance_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE lesson_entry_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE message_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE message_attachment_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE message_file_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth_client_info_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE privacy_category_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE resource_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE resource_reservation_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE resource_type_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE room_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE room_tag_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE setting_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE sick_note_attachment_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE student_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE study_group_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE study_group_membership_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE subject_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE substitution_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE teacher_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE teacher_tag_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable_lesson_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable_period_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable_supervision_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable_week_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE tuition_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE wiki_audit CHANGE diffs diffs LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE appointment_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE appointment_category_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE book_comment_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE display_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE document_attachment_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE document_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE document_category_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE exam_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE exam_supervision_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE excuse_note_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE free_timespan_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE grade_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE grade_membership_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE grade_teacher_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE infotext_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE lesson_attendance_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE lesson_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE lesson_entry_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE message_attachment_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE message_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE message_file_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE oauth_client_info_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE privacy_category_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE resource_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE resource_reservation_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE resource_type_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE room_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE room_tag_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE setting_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE sick_note_attachment_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE student_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE study_group_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE study_group_membership_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE subject_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE substitution_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE teacher_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE teacher_tag_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE timetable_lesson_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE timetable_period_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE timetable_supervision_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE timetable_week_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE tuition_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE wiki_audit CHANGE diffs diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
    }
}
