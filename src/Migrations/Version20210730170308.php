<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210730170308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson_entry DROP FOREIGN KEY FK_61C749F7CDF80196');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F7CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson_entry DROP FOREIGN KEY FK_61C749F7CDF80196');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F7CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
    }
}
