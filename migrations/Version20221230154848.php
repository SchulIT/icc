<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221230154848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cron_job_result');
        $this->addSql('DROP TABLE cron_job');

        $this->addSql('CREATE TABLE cron_job (id int(10) unsigned NOT NULL AUTO_INCREMENT, command varchar(255) NOT NULL, arguments varchar(255) DEFAULT NULL, description varchar(255) DEFAULT NULL,  `running_instances` int(10) unsigned NOT NULL DEFAULT 0, max_instances int(10) unsigned NOT NULL DEFAULT 1, number int(10) unsigned NOT NULL DEFAULT 1, `period` varchar(255) NOT NULL, last_use datetime DEFAULT NULL, next_run datetime NOT NULL, enable tinyint(1) NOT NULL DEFAULT 1, created_at datetime NOT NULL, updated_at datetime NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $this->addSql('CREATE TABLE IF NOT EXISTS `cron_job_result` (id int(10) unsigned NOT NULL AUTO_INCREMENT,  cron_job_id int(10) unsigned NOT NULL, run_at datetime NOT NULL, run_time double NOT NULL, status_code int(11) NOT NULL, output longtext DEFAULT NULL, created_at datetime NOT NULL, updated_at datetime NOT NULL, PRIMARY KEY (`id`), KEY `IDX_2CD346EE79099ED8` (`cron_job_id`), CONSTRAINT `FK_2CD346EE79099ED8` FOREIGN KEY (`cron_job_id`) REFERENCES `cron_job` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Diese Migration kann nicht rückgängig gemacht werden.');
    }
}
