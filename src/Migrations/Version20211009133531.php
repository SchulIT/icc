<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211009133531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE FULLTEXT INDEX IDX_B6BD307F2B36786B ON message (title)');
        $this->addSql('CREATE FULLTEXT INDEX IDX_B6BD307FFEC530A9 ON message (content)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_B6BD307F2B36786B ON message');
        $this->addSql('DROP INDEX IDX_B6BD307FFEC530A9 ON message');
    }
}
