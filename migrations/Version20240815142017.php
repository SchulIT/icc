<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240815142017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_absence_comment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, absence_id INT UNSIGNED NOT NULL, tuition_id INT UNSIGNED DEFAULT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, comment LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_1CA1AD702DFF238F (absence_id), INDEX IDX_1CA1AD707FFA6BA (tuition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable_lesson_additional_information (id INT UNSIGNED AUTO_INCREMENT NOT NULL, study_group_id INT UNSIGNED NOT NULL, author_id INT UNSIGNED NOT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, comment_teacher LONGTEXT DEFAULT NULL, comment_students LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_92BAA135DDDCCCE (study_group_id), INDEX IDX_92BAA13F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_absence_comment ADD CONSTRAINT FK_1CA1AD702DFF238F FOREIGN KEY (absence_id) REFERENCES teacher_absence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_absence_comment ADD CONSTRAINT FK_1CA1AD707FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE timetable_lesson_additional_information ADD CONSTRAINT FK_92BAA135DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_lesson_additional_information ADD CONSTRAINT FK_92BAA13F675F31B FOREIGN KEY (author_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_absence_lesson DROP FOREIGN KEY FK_25D18E2BCDF80196');
        $this->addSql('ALTER TABLE teacher_absence_lesson DROP FOREIGN KEY FK_25D18E2B2DFF238F');
        $this->addSql('ALTER TABLE teacher_absence_lesson DROP FOREIGN KEY FK_25D18E2B7FFA6BA');
        $this->addSql('DROP TABLE teacher_absence_lesson');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_absence_lesson (id INT UNSIGNED AUTO_INCREMENT NOT NULL, absence_id INT UNSIGNED NOT NULL, lesson_id INT UNSIGNED DEFAULT NULL, tuition_id INT UNSIGNED DEFAULT NULL, comment_teacher LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, comment_students LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, INDEX IDX_25D18E2B2DFF238F (absence_id), INDEX IDX_25D18E2BCDF80196 (lesson_id), INDEX IDX_25D18E2B7FFA6BA (tuition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE teacher_absence_lesson ADD CONSTRAINT FK_25D18E2BCDF80196 FOREIGN KEY (lesson_id) REFERENCES timetable_lesson (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE teacher_absence_lesson ADD CONSTRAINT FK_25D18E2B2DFF238F FOREIGN KEY (absence_id) REFERENCES teacher_absence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_absence_lesson ADD CONSTRAINT FK_25D18E2B7FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE teacher_absence_comment DROP FOREIGN KEY FK_1CA1AD702DFF238F');
        $this->addSql('ALTER TABLE teacher_absence_comment DROP FOREIGN KEY FK_1CA1AD707FFA6BA');
        $this->addSql('ALTER TABLE timetable_lesson_additional_information DROP FOREIGN KEY FK_92BAA135DDDCCCE');
        $this->addSql('ALTER TABLE timetable_lesson_additional_information DROP FOREIGN KEY FK_92BAA13F675F31B');
        $this->addSql('DROP TABLE teacher_absence_comment');
        $this->addSql('DROP TABLE timetable_lesson_additional_information');
    }
}
