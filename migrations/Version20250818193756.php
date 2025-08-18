<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250818193756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY `FK_2CD346EE79099ED8`');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_job_result');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cron_job (id INT UNSIGNED AUTO_INCREMENT NOT NULL, command VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, arguments VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, running_instances INT UNSIGNED DEFAULT 0 NOT NULL, max_instances INT UNSIGNED DEFAULT 1 NOT NULL, number INT UNSIGNED DEFAULT 1 NOT NULL, period VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, last_use DATETIME DEFAULT NULL, next_run DATETIME NOT NULL, enable TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cron_job_result (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cron_job_id INT UNSIGNED NOT NULL, run_at DATETIME NOT NULL, run_time DOUBLE PRECISION NOT NULL, status_code INT NOT NULL, output LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2CD346EE79099ED8 (cron_job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT `FK_2CD346EE79099ED8` FOREIGN KEY (cron_job_id) REFERENCES cron_job (id) ON DELETE CASCADE');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
