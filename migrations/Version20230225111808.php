<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230225111808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_absence (id INT UNSIGNED AUTO_INCREMENT NOT NULL, teacher_id INT UNSIGNED DEFAULT NULL, type_id INT UNSIGNED DEFAULT NULL, processed_by_id INT UNSIGNED DEFAULT NULL, message LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, processed_at DATETIME DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', from_date DATE NOT NULL, from_lesson INT NOT NULL, until_date DATE NOT NULL, until_lesson INT NOT NULL, INDEX IDX_BB986D8841807E1D (teacher_id), INDEX IDX_BB986D88C54C8C93 (type_id), INDEX IDX_BB986D882FFD4FD3 (processed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teacher_absence_lesson (id INT UNSIGNED AUTO_INCREMENT NOT NULL, absence_id INT UNSIGNED DEFAULT NULL, lesson_id INT UNSIGNED DEFAULT NULL, comment_teacher VARCHAR(255) DEFAULT NULL, comment_students VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_25D18E2B2DFF238F (absence_id), INDEX IDX_25D18E2BCDF80196 (lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teacher_absence_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_absence ADD CONSTRAINT FK_BB986D8841807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_absence ADD CONSTRAINT FK_BB986D88C54C8C93 FOREIGN KEY (type_id) REFERENCES teacher_absence_type (id)');
        $this->addSql('ALTER TABLE teacher_absence ADD CONSTRAINT FK_BB986D882FFD4FD3 FOREIGN KEY (processed_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE teacher_absence_lesson ADD CONSTRAINT FK_25D18E2B2DFF238F FOREIGN KEY (absence_id) REFERENCES teacher_absence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_absence_lesson ADD CONSTRAINT FK_25D18E2BCDF80196 FOREIGN KEY (lesson_id) REFERENCES timetable_lesson (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher_absence DROP FOREIGN KEY FK_BB986D8841807E1D');
        $this->addSql('ALTER TABLE teacher_absence DROP FOREIGN KEY FK_BB986D88C54C8C93');
        $this->addSql('ALTER TABLE teacher_absence DROP FOREIGN KEY FK_BB986D882FFD4FD3');
        $this->addSql('ALTER TABLE teacher_absence_lesson DROP FOREIGN KEY FK_25D18E2B2DFF238F');
        $this->addSql('ALTER TABLE teacher_absence_lesson DROP FOREIGN KEY FK_25D18E2BCDF80196');
        $this->addSql('DROP TABLE teacher_absence');
        $this->addSql('DROP TABLE teacher_absence_lesson');
        $this->addSql('DROP TABLE teacher_absence_type');
    }
}
