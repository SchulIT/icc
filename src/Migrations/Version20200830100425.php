<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200830100425 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2628C7279F75D7B0 ON timetable_lesson');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2628C7279F75D7B0EC8B7ADE ON timetable_lesson (external_id, period_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2628C7279F75D7B0EC8B7ADE ON timetable_lesson');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2628C7279F75D7B0 ON timetable_lesson (external_id)');
    }
}
