<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210730160037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sick_note DROP FOREIGN KEY FK_6C5484DDCB944F1A');
        $this->addSql('ALTER TABLE sick_note ADD CONSTRAINT FK_6C5484DDCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_web_push_subscription DROP FOREIGN KEY FK_260A81F9A76ED395');
        $this->addSql('ALTER TABLE user_web_push_subscription ADD CONSTRAINT FK_260A81F9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sick_note DROP FOREIGN KEY FK_6C5484DDCB944F1A');
        $this->addSql('ALTER TABLE sick_note ADD CONSTRAINT FK_6C5484DDCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE user_web_push_subscription DROP FOREIGN KEY FK_260A81F9A76ED395');
        $this->addSql('ALTER TABLE user_web_push_subscription ADD CONSTRAINT FK_260A81F9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
