<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210731203105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tuition_teachers (tuition_id INT UNSIGNED NOT NULL, teacher_id INT UNSIGNED NOT NULL, INDEX IDX_16C6F80B7FFA6BA (tuition_id), INDEX IDX_16C6F80B41807E1D (teacher_id), PRIMARY KEY(tuition_id, teacher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tuition_teachers ADD CONSTRAINT FK_16C6F80B7FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tuition_teachers ADD CONSTRAINT FK_16C6F80B41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tuition_additional_teachers');
        $this->addSql('ALTER TABLE tuition DROP FOREIGN KEY FK_A1B25E5B41807E1D');
        $this->addSql('DROP INDEX IDX_A1B25E5B41807E1D ON tuition');
        $this->addSql('ALTER TABLE tuition DROP teacher_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tuition_additional_teachers (tuition_id INT UNSIGNED NOT NULL, teacher_id INT UNSIGNED NOT NULL, INDEX IDX_CD02805441807E1D (teacher_id), INDEX IDX_CD0280547FFA6BA (tuition_id), PRIMARY KEY(tuition_id, teacher_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tuition_additional_teachers ADD CONSTRAINT FK_CD02805441807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tuition_additional_teachers ADD CONSTRAINT FK_CD0280547FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tuition_teachers');
        $this->addSql('ALTER TABLE tuition ADD teacher_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE tuition ADD CONSTRAINT FK_A1B25E5B41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A1B25E5B41807E1D ON tuition (teacher_id)');
    }
}
