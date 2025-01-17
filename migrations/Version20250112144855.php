<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112144855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE checklist (id INT UNSIGNED AUTO_INCREMENT NOT NULL, created_by_id INT UNSIGNED NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, due_date DATE DEFAULT NULL, can_students_view TINYINT(1) NOT NULL, can_parents_view TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_5C696D2FB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE checklist_user (checklist_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_B63D3AEB16D08A7 (checklist_id), INDEX IDX_B63D3AEA76ED395 (user_id), PRIMARY KEY(checklist_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE checklist_student (checklist_id INT UNSIGNED NOT NULL, student_id INT UNSIGNED NOT NULL, is_checked TINYINT(1) NOT NULL, comment VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DB446FC9B16D08A7 (checklist_id), INDEX IDX_DB446FC9CB944F1A (student_id), PRIMARY KEY(checklist_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE checklist ADD CONSTRAINT FK_5C696D2FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE checklist_user ADD CONSTRAINT FK_B63D3AEB16D08A7 FOREIGN KEY (checklist_id) REFERENCES checklist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE checklist_user ADD CONSTRAINT FK_B63D3AEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE checklist_student ADD CONSTRAINT FK_DB446FC9B16D08A7 FOREIGN KEY (checklist_id) REFERENCES checklist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE checklist_student ADD CONSTRAINT FK_DB446FC9CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checklist DROP FOREIGN KEY FK_5C696D2FB03A8386');
        $this->addSql('ALTER TABLE checklist_user DROP FOREIGN KEY FK_B63D3AEB16D08A7');
        $this->addSql('ALTER TABLE checklist_user DROP FOREIGN KEY FK_B63D3AEA76ED395');
        $this->addSql('ALTER TABLE checklist_student DROP FOREIGN KEY FK_DB446FC9B16D08A7');
        $this->addSql('ALTER TABLE checklist_student DROP FOREIGN KEY FK_DB446FC9CB944F1A');
        $this->addSql('DROP TABLE checklist');
        $this->addSql('DROP TABLE checklist_user');
        $this->addSql('DROP TABLE checklist_student');
    }
}
