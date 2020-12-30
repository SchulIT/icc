<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229211152 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE resource_reservation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, resource_id INT UNSIGNED DEFAULT NULL, teacher_id INT UNSIGNED DEFAULT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_4673770789329D25 (resource_id), INDEX IDX_4673770741807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_reservation_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_70ab199a84133b5949a9963b5d5ee78a_idx (type), INDEX object_id_70ab199a84133b5949a9963b5d5ee78a_idx (object_id), INDEX discriminator_70ab199a84133b5949a9963b5d5ee78a_idx (discriminator), INDEX transaction_hash_70ab199a84133b5949a9963b5d5ee78a_idx (transaction_hash), INDEX blame_id_70ab199a84133b5949a9963b5d5ee78a_idx (blame_id), INDEX created_at_70ab199a84133b5949a9963b5d5ee78a_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resource_reservation ADD CONSTRAINT FK_4673770789329D25 FOREIGN KEY (resource_id) REFERENCES resource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource_reservation ADD CONSTRAINT FK_4673770741807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE room_reservation');
        $this->addSql('DROP TABLE room_reservation_audit');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_reservation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED DEFAULT NULL, teacher_id INT UNSIGNED DEFAULT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_56FDE76A54177093 (room_id), INDEX IDX_56FDE76A41807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE room_reservation_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX blame_id_03d7cafe07b2d5a503bd3850fc4ba3cf_idx (blame_id), INDEX discriminator_03d7cafe07b2d5a503bd3850fc4ba3cf_idx (discriminator), INDEX created_at_03d7cafe07b2d5a503bd3850fc4ba3cf_idx (created_at), INDEX type_03d7cafe07b2d5a503bd3850fc4ba3cf_idx (type), INDEX transaction_hash_03d7cafe07b2d5a503bd3850fc4ba3cf_idx (transaction_hash), INDEX object_id_03d7cafe07b2d5a503bd3850fc4ba3cf_idx (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE room_reservation ADD CONSTRAINT FK_56FDE76A41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_reservation ADD CONSTRAINT FK_56FDE76A54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE resource_reservation');
        $this->addSql('DROP TABLE resource_reservation_audit');
    }
}
