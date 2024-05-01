<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240501111840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    private array $existingValues = [ ];

    public function preUp(Schema $schema): void {
        $result = $this->connection->executeQuery('SELECT id, `values` FROM tuition_grade_type');
        $this->existingValues = $result->fetchAllAssociative();
    }

    public function postUp(Schema $schema): void {
        foreach($this->existingValues as $existingValue) {
            $values = json_decode($existingValue['values'], true);

            foreach($values as $value) {
                $stmt = $this->connection->prepare('INSERT INTO tuition_grade_catalog_grade (`catalog_id`, `value`) VALUES(:catalog, :value)');
                $stmt->bindValue('catalog', $existingValue['id']);
                $stmt->bindValue('value', $value);
                $stmt->executeQuery();
            }
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('RENAME TABLE tuition_grade_type TO tuition_grade_catalog');
        $this->addSql('CREATE TABLE tuition_grade_catalog_grade (id INT UNSIGNED AUTO_INCREMENT NOT NULL, catalog_id INT UNSIGNED DEFAULT NULL, value VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, INDEX IDX_5BFA54E6CC3C66FC (catalog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade ADD CONSTRAINT FK_5BFA54E6CC3C66FC FOREIGN KEY (catalog_id) REFERENCES tuition_grade_catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tuition_grade_catalog DROP `values`');
        $this->addSql('CREATE TABLE tuition_grade_catalog_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_8316d09e5cc68d88cf9edd5ec594bcec_idx (type), INDEX object_id_8316d09e5cc68d88cf9edd5ec594bcec_idx (object_id), INDEX discriminator_8316d09e5cc68d88cf9edd5ec594bcec_idx (discriminator), INDEX transaction_hash_8316d09e5cc68d88cf9edd5ec594bcec_idx (transaction_hash), INDEX blame_id_8316d09e5cc68d88cf9edd5ec594bcec_idx (blame_id), INDEX created_at_8316d09e5cc68d88cf9edd5ec594bcec_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE tuition_grade_type_audit');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade DROP FOREIGN KEY FK_5BFA54E6CC3C66FC');
        $this->addSql('DROP TABLE tuition_grade_catalog_grade');
        $this->addSql('ALTER TABLE tuition_grade_type ADD `values` JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('RENAME TABLE tuition_grade_catalog TO tuition_grade_type');
        $this->addSql('CREATE TABLE tuition_grade_type_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX object_id_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (object_id), INDEX created_at_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (created_at), INDEX discriminator_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (discriminator), INDEX transaction_hash_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (transaction_hash), INDEX type_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (type), INDEX blame_id_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (blame_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE tuition_grade_catalog_audit');
    }
}
