<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512130043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function postUp(Schema $schema): void {
        $stmt = $this->connection->executeQuery('UPDATE chat c SET c.created_by_id = (SELECT created_by_id FROM chat_message m WHERE m.chat_id = c.id ORDER BY id ASC LIMIT 1)');
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat ADD created_by_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_659DF2AAB03A8386 ON chat (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAB03A8386');
        $this->addSql('DROP INDEX IDX_659DF2AAB03A8386 ON chat');
        $this->addSql('ALTER TABLE chat DROP created_by_id');
    }
}
