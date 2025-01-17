<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240815120306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_event (id INT UNSIGNED AUTO_INCREMENT NOT NULL, teacher_id INT UNSIGNED DEFAULT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_356C1FDE41807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_event ADD CONSTRAINT FK_356C1FDE41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendance ADD event_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9171F7E88B FOREIGN KEY (event_id) REFERENCES book_event (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6DE30D9171F7E88B ON attendance (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D9171F7E88B');
        $this->addSql('ALTER TABLE book_event DROP FOREIGN KEY FK_356C1FDE41807E1D');
        $this->addSql('DROP TABLE book_event');
        $this->addSql('DROP INDEX IDX_6DE30D9171F7E88B ON attendance');
        $this->addSql('ALTER TABLE attendance DROP event_id');
    }
}
