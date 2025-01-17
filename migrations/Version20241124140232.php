<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124140232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resource_reservation ADD associated_study_group_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE resource_reservation ADD CONSTRAINT FK_46737707DBA6DCCE FOREIGN KEY (associated_study_group_id) REFERENCES study_group (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_46737707DBA6DCCE ON resource_reservation (associated_study_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resource_reservation DROP FOREIGN KEY FK_46737707DBA6DCCE');
        $this->addSql('DROP INDEX IDX_46737707DBA6DCCE ON resource_reservation');
        $this->addSql('ALTER TABLE resource_reservation DROP associated_study_group_id');
    }
}
