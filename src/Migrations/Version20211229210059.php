<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211229210059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE excuse_note ADD until_date DATE NOT NULL, CHANGE lesson_end until_lesson INT NOT NULL, CHANGE lesson_start from_lesson INT NOT NULL, CHANGE date from_date DATE NOT NULL');
        $this->addSql('UPDATE excuse_note SET until_date = from_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE excuse_note CHANGE from_date date DATE NOT NULL, CHANGE from_lesson lesson_start INT NOT NULL, CHANGE until_lesson lesson_end INT NOT NULL, DROP until_date');
    }
}
