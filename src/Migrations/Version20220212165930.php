<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220212165930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id)');
    }
}
