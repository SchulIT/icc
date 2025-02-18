<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218173002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE processed_messages (id INT UNSIGNED AUTO_INCREMENT NOT NULL, run_id INT NOT NULL, attempt SMALLINT NOT NULL, message_type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, dispatched_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', received_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', finished_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', wait_time BIGINT NOT NULL, handle_time BIGINT NOT NULL, memory_usage INT NOT NULL, transport VARCHAR(255) NOT NULL, tags VARCHAR(255) DEFAULT NULL, failure_type VARCHAR(255) DEFAULT NULL, failure_message LONGTEXT DEFAULT NULL, results JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE processed_messages');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
