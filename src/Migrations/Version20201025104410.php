<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025104410 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE substitution ADD room_name VARCHAR(255) DEFAULT NULL COMMENT \'Plain room name in case room resolve is not possible when importing substitutions.\', ADD replacement_room_name VARCHAR(255) DEFAULT NULL COMMENT \'Plain room name in case room resolve is not possible when importing substitutions.\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE substitution DROP room_name, DROP replacement_room_name');
    }
}
