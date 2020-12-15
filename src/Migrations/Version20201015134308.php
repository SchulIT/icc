<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201015134308 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD is_confirmed TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE appointment ADD created_by_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FE38F844B03A8386 ON appointment (created_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP is_confirmed');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844B03A8386');
        $this->addSql('DROP INDEX IDX_FE38F844B03A8386 ON appointment');
        $this->addSql('ALTER TABLE appointment DROP created_by_id');
    }
}
