<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209153849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendance_excuse_note (attendance_id INT UNSIGNED NOT NULL, excuse_note_id INT UNSIGNED NOT NULL, INDEX IDX_EC6DA429163DDA15 (attendance_id), INDEX IDX_EC6DA429D586BB12 (excuse_note_id), PRIMARY KEY (attendance_id, excuse_note_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE attendance_excuse_note ADD CONSTRAINT FK_EC6DA429163DDA15 FOREIGN KEY (attendance_id) REFERENCES attendance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendance_excuse_note ADD CONSTRAINT FK_EC6DA429D586BB12 FOREIGN KEY (excuse_note_id) REFERENCES excuse_note (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendance_excuse_note DROP FOREIGN KEY FK_EC6DA429163DDA15');
        $this->addSql('ALTER TABLE attendance_excuse_note DROP FOREIGN KEY FK_EC6DA429D586BB12');
        $this->addSql('DROP TABLE attendance_excuse_note');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
