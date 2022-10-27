<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200822133206 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE timetable_lesson_grades (timetable_lesson_id INT UNSIGNED NOT NULL, grade_id INT UNSIGNED NOT NULL, INDEX IDX_B1CF3BAAEA4D7C00 (timetable_lesson_id), INDEX IDX_B1CF3BAAFE19A1A8 (grade_id), PRIMARY KEY(timetable_lesson_id, grade_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE timetable_lesson_grades ADD CONSTRAINT FK_B1CF3BAAEA4D7C00 FOREIGN KEY (timetable_lesson_id) REFERENCES timetable_lesson (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_lesson_grades ADD CONSTRAINT FK_B1CF3BAAFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_lesson DROP FOREIGN KEY FK_2628C7277FFA6BA');
        $this->addSql('ALTER TABLE timetable_lesson ADD subject_id INT UNSIGNED DEFAULT NULL, DROP type, DROP subject');
        $this->addSql('ALTER TABLE timetable_lesson ADD CONSTRAINT FK_2628C72723EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE timetable_lesson ADD CONSTRAINT FK_2628C7277FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2628C72723EDC87 ON timetable_lesson (subject_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE timetable_lesson_grades');
        $this->addSql('ALTER TABLE timetable_lesson DROP FOREIGN KEY FK_2628C72723EDC87');
        $this->addSql('ALTER TABLE timetable_lesson DROP FOREIGN KEY FK_2628C7277FFA6BA');
        $this->addSql('DROP INDEX IDX_2628C72723EDC87 ON timetable_lesson');
        $this->addSql('ALTER TABLE timetable_lesson ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD subject VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP subject_id');
        $this->addSql('ALTER TABLE timetable_lesson ADD CONSTRAINT FK_2628C7277FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE CASCADE');
    }
}
